<?php

declare(strict_types=1);

namespace Pinto\Attribute\ObjectType;

use Pinto\Exception\PintoThemeDefinition;
use Pinto\Exception\Slots\BuildValidation;
use Pinto\List\ObjectListInterface;
use Pinto\ObjectType\ObjectTypeInterface;
use Pinto\Slots\Build;
use Pinto\Slots\Definition;

/**
 * An attribute representing an object with slots.
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS_CONSTANT)]
final class Slots implements ObjectTypeInterface
{
    /**
     * Constructs a Slots attribute.
     *
     * @param string|null $method
     *   Specify the name of a method to reflect slots from. This is only used
     *   when the attribute is on an enum, not individual theme objects.
     */
    public function __construct(
        public ?string $method = null,
    ) {
    }

    public static function createBuild(ObjectListInterface $case, string $objectClassName): mixed
    {
        return Build::create();
    }

    public static function validateBuild(mixed $build, mixed $definition, string $objectClassName): void
    {
        // @todo validate typing?
        assert($build instanceof Build);
        assert($definition instanceof Definition);

        $missingSlots = [];
        // @todo adapt to Enum-keys.
        foreach (\array_keys($definition->slots) as $slot) {
            if (false === $build->pintoHas($slot)) {
                // @todo adapt to Enum-keys.
                $missingSlots[] = $slot;
            }
        }

        if ([] !== $missingSlots) {
            throw BuildValidation::missingSlots($objectClassName, $missingSlots);
        }
    }

    public function getDefinition(ObjectListInterface $case, \Reflector $r): mixed
    {
        $reflectionMethod = match (true) {
            $r instanceof \ReflectionClass && null !== $this->method => $r->getMethod($this->method),
            $r instanceof \ReflectionClass => $r->getConstructor() ?? throw new PintoThemeDefinition('Missing method to reflect parameters from.'),
            $r instanceof \ReflectionMethod => $r,
            default => throw new \LogicException('Unsupported reflection: ' . $r::class),
        };

        if (true !== $reflectionMethod->isPublic()) {
            throw new PintoThemeDefinition(sprintf('Method %s::%s() must be public to be used as a %s entrypoint.', $reflectionMethod->getDeclaringClass()->getName(), $reflectionMethod->getShortName(), static::class));
        }

        $slots = [];
        foreach ($reflectionMethod->getParameters() as $rParam) {
            $paramType = $rParam->getType();
            if ($paramType instanceof \ReflectionNamedType) {
                $slots[$rParam->getName()] = [
                    'type' => $paramType->getName(),
                    'default' => $rParam->isDefaultValueAvailable() ? $rParam->getDefaultValue() : null,
                ];
            }
        }

        return new Definition($slots);
    }
}
