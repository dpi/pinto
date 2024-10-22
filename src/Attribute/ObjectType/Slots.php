<?php

declare(strict_types=1);

namespace Pinto\Attribute\ObjectType;

use Pinto\Exception\PintoThemeDefinition;
use Pinto\Exception\Slots\BuildValidation;
use Pinto\List\ObjectListInterface;
use Pinto\ObjectType\ObjectTypeInterface;
use Pinto\Slots\Build;
use Pinto\Slots\Definition;
use Pinto\Slots\NoDefaultValue;
use Pinto\Slots\Slot;
use Pinto\Slots\SlotList;

/**
 * An attribute representing an object with slots.
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS_CONSTANT)]
final class Slots implements ObjectTypeInterface
{
    private const useNamedParameters = 'Using this attribute without named parameters is not supported.';

    public SlotList $slots;

    /**
     * Constructs a Slots attribute.
     *
     * @param list<\Pinto\Slots\Slot|string|\UnitEnum> $slots
     *   The list of slots for the object.
     *   If omitted, slots will be reflected:
     *     - from the class constructor when the attribute is on the class, or,
     *     - a method if the attribute is on a method or $method parameter is
     *       supplied.
     *   Slots may be string or enums or \Pinto\Slots\Slot objects. Using string
     *   or enum slots is a simplified way of defining slots. To provide other
     *   options like default values, the \Pinto\Slots\Slot object or reflection
     *   (by omitting $slots) must be used.
     * @param string|null $method
     *   Specify the name of a method to reflect slots from. This is only used
     *   when the attribute is on an enum, not individual theme objects.
     */
    public function __construct(
        string $useNamedParameters = self::useNamedParameters,
        $slots = [],
        public ?string $method = null,
    ) {
        if (self::useNamedParameters !== $useNamedParameters) {
            throw new \LogicException(self::useNamedParameters);
        }

        $this->slots = new SlotList();
        foreach ($slots as $slot) {
            $this->slots->add(
                $slot instanceof Slot ? $slot : new Slot(name: $slot)
            );
        }
    }

    public static function createBuild(ObjectListInterface $case, mixed $definition, string $objectClassName): mixed
    {
        assert($definition instanceof Definition);

        $build = Build::create(slotList: $definition->slots);

        foreach ($definition->slots as $slot) {
            if (!$slot->defaultValue instanceof NoDefaultValue) {
                $build->set($slot->name, $slot->defaultValue);
            }
        }

        return $build;
    }

    public static function validateBuild(mixed $build, mixed $definition, string $objectClassName): void
    {
        // @todo validate typing?
        assert($build instanceof Build);
        assert($definition instanceof Definition);

        $missingSlots = [];

        foreach ($definition->slots as $slot) {
            // When there is no default, the slot must be defined:
            if ($slot->defaultValue instanceof NoDefaultValue && false === $build->pintoHas($slot->name)) {
                $missingSlots[] = $slot->name instanceof \UnitEnum ? $slot->name->name : $slot->name;
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

        $slots = $this->slots;

        // When no slots were provided to the attribute, use reflection:
        if (0 === $this->slots->count()) {
            $slots = new SlotList();
            foreach ($reflectionMethod->getParameters() as $rParam) {
                $paramType = $rParam->getType();
                if ($paramType instanceof \ReflectionNamedType) {
                    // @todo use the type @ $paramType->getName()
                    $args = ['name' => $rParam->getName()];
                    // Default should only be set if there is a default.
                    if ($rParam->isDefaultValueAvailable()) {
                        $args['defaultValue'] = $rParam->getDefaultValue();
                    }

                    $slots[] = new Slot(...$args);
                }
            }
        }

        return new Definition($slots);
    }
}
