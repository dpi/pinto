<?php

declare(strict_types=1);

namespace Pinto\ObjectType;

use Pinto\DefinitionDiscovery;
use Pinto\Exception\PintoThemeDefinition;
use Pinto\List\ObjectListInterface;
use Pinto\PintoMapping;

/**
 * Discovers ObjectTypeInterface attributes on a class, on a class or its methods.
 *
 * @internal
 */
final class ObjectTypeDiscovery
{
    /**
     * Finds the entry point for an object, which specifies the object type.
     *
     * Looks for a ObjectListInterface for an enum.
     *
     * In order:
     *   - On the class and each public method.
     *     - If multiple are found, exception, or returns the single found. If none are found:
     *   - On the list:
     *     For each of these, the 'method' override in #[Slots] can be used to specify an entrypoint on the object class
     *     other than the constructor.
     *     - The enum case, otherwise:
     *     - On the enum class
     *   - Otherwise throws an exception.
     *
     * @param class-string $objectClassName
     *
     * @return array{class-string<\Pinto\ObjectType\ObjectTypeInterface>, mixed}
     *
     * @throws PintoThemeDefinition
     */
    public static function definitionForThemeObject(string $objectClassName, ObjectListInterface $case, DefinitionDiscovery $definitionDiscovery, ?ObjectListInterface $originalCase = null): array
    {
        /** @var array<array{\ReflectionAttribute<\Pinto\ObjectType\ObjectTypeInterface>, \Reflector}> $definitions */
        $definitions = [];

        // Look for attribute instances of ObjectTypeInterface attributes on the class and all
        // methods together. At the end an exception is thrown if multiple were found.

        // Look for attribute instances of ObjectTypeInterface on the class itself.
        $objectClassReflection = new \ReflectionClass($objectClassName);
        array_push($definitions, ...array_map(
            fn (\ReflectionAttribute $r): array => [$r, $objectClassReflection],
            $objectClassReflection->getAttributes(ObjectTypeInterface::class, \ReflectionAttribute::IS_INSTANCEOF),
        ));

        // Look for attribute instances of ObjectTypeInterface on public methods.
        $methods = $objectClassReflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $reflectionMethod) {
            array_push($definitions, ...array_map(
                static fn (\ReflectionAttribute $r): array => [$r, $reflectionMethod],
                $reflectionMethod->getAttributes(ObjectTypeInterface::class, \ReflectionAttribute::IS_INSTANCEOF),
            ));
        }

        if (count($definitions) > 1) {
            throw new PintoThemeDefinition(sprintf('Multiple theme definitions found on %s. There must only be one.', $objectClassName));
        } elseif (1 === count($definitions)) {
            return [$definitions[0][0]->getName(), $definitions[0][0]->newInstance()->getDefinition($originalCase ?? $case, $definitions[0][1])];
        }

        // Otherwise defer to parent if it was provided (this isn't recurison).
        $extendsObject = $definitionDiscovery->extendsKnownObject($objectClassName);
        if ($extendsObject !== null) {
          return static::definitionForThemeObject($extendsObject, $definitionDiscovery[$extendsObject], $definitionDiscovery, originalCase: $case);
        }

        // Try the enum case.
        $rCase = new \ReflectionEnumUnitCase($case::class, $case->name);
        $attr = $rCase->getAttributes(ObjectTypeInterface::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;
        $slotInstance = $attr?->newInstance();
        if (null !== $slotInstance) {
            return [$attr->getName(), $slotInstance->getDefinition($originalCase ?? $case, $objectClassReflection)];
        }

        // Try the enum.
        $enumClassName = $case::class;
        $rEnum = new \ReflectionClass($enumClassName);
        $attr = $rEnum->getAttributes(ObjectTypeInterface::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;
        $slotInstance = $attr?->newInstance();
        if (null !== $slotInstance) {
            return [$attr->getName(), $slotInstance->getDefinition($originalCase ?? $case, $objectClassReflection)];
        }

        throw new PintoThemeDefinition(sprintf('Missing %s attribute on %s or %s or %s', ObjectTypeInterface::class, $objectClassName, $enumClassName, sprintf('%s::%s', $case::class, $case->name)));
    }
}
