<?php

declare(strict_types=1);

namespace Pinto\Attribute\ObjectType;

use Pinto\Exception\PintoBuildDefinitionMismatch;
use Pinto\Exception\PintoThemeDefinition;
use Pinto\List\ObjectListInterface;
use Pinto\ObjectType\ObjectTypeInterface;
use Pinto\ThemeDefinition\HookThemeDefinition;

/**
 * Trait for theme definition object types.
 *
 * This code will be deleted and moved out of this project in the future.
 *
 * @internal
 */
trait LegacyThemeDefinitionTrait
{
    /**
     * Defines a theme definition.
     *
     * @param array<mixed>|null $definition
     *   Definition required when attached to a class. Otherwise, must be NULL.
     */
    final public function __construct(
        public ?array $definition = null,
    ) {
    }

    final public static function createBuild(ObjectListInterface $case, string $objectClassName): mixed
    {
        return [
            '#theme' => $case->name(),
            '#attached' => ['library' => $case->attachLibraries()],
        ];
    }

    final public static function validateBuild(mixed $build, mixed $definition, string $objectClassName): void
    {
        /* @phpstan-assert array{variables?: array<mixed>, path: string, template: string} $definition */
        if (false === is_array($build)) {
            // Allow build to be something other than an array.
            return;
        }

        if (!$definition instanceof HookThemeDefinition) {
            // Impossible, but for Stan.
            throw new \LogicException('Definition should be a ' . HookThemeDefinition::class);
        }

        $themeDefinitionKeysForComparison = array_map(fn (string $varName): string => '#' . $varName, array_keys($definition->definition['variables'] ?? []));

        // @todo assert keys in $built map those in themeDefinition()
        // allow extra keys ( things like # cache).
        // But dont allow missing keys.
        $missingKeys = array_diff($themeDefinitionKeysForComparison, array_keys($build));
        if (count($missingKeys) > 0) {
            throw new PintoBuildDefinitionMismatch($objectClassName, $missingKeys);
        }
    }

    final public function getDefinition(ObjectListInterface $case, \Reflector $r): mixed
    {
        if ($r instanceof \ReflectionClass) {
            $definition = $this->definition ?? throw new PintoThemeDefinition('$definition property must be set for ' . ObjectTypeInterface::class . ' attributes on the class level of a theme object.');
        } elseif ($r instanceof \ReflectionMethod) {
            if (null !== $this->definition) {
                throw new PintoThemeDefinition(sprintf('%s attribute must not have $definition set on %s::%s.', ObjectTypeInterface::class, $r->getDeclaringClass()->getName(), $r->getName()));
            }

            if (false === $r->isStatic()) {
                throw new PintoThemeDefinition(sprintf('%s attribute must be attached to a static method. %s::%s is not static.', ObjectTypeInterface::class, $r->getDeclaringClass()->getName(), $r->getName()));
            }

            // Call the method directly and get the theme definition.
            $definition = $r->invoke(null);
        }

        return new HookThemeDefinition(definition: ($definition ?? []) + [
            'variables' => [],
            'path' => $case->templateDirectory(),
            'template' => $case->templateName(),
        ]);
    }
}
