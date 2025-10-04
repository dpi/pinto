<?php

declare(strict_types=1);

namespace Pinto\Attribute\ObjectType;

use Pinto\Exception\PintoThemeDefinition;
use Pinto\Exception\Slots\BuildValidation;
use Pinto\List\ObjectListInterface;
use Pinto\ObjectType\LateBindObjectContext;
use Pinto\ObjectType\ObjectTypeInterface;
use Pinto\Slots\Attribute\ModifySlots;
use Pinto\Slots\Attribute\RenameSlot;
use Pinto\Slots\Build;
use Pinto\Slots\Definition;
use Pinto\Slots\NoDefaultValue;
use Pinto\Slots\Origin;
use Pinto\Slots\RenameSlots;
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

    /**
     * Constructs a Slots attribute.
     *
     * @param list<Slot|string|\UnitEnum|class-string<\UnitEnum>> $slots
     *   The list of slots for the object.
     *   If omitted, slots will be reflected:
     *     - from the class constructor when the attribute is on the class, or,
     *     - a method if the attribute is on a method or $method parameter is supplied.
     *   Slots may be any mix of \Pinto\Slots\Slot objects, string, enums, or enum class-strings. Using string, enum,
     *   enum class-string slots are a simplified way of defining slots. To provide other options like default values,
     *   the \Pinto\Slots\Slot object or reflection (by omitting $slots) must be used.
     * @param string|null $method
     *   Specify the name of a method to reflect slots from. This is only used when the attribute is on an enum, not individual theme objects.
     * @param bool $bindPromotedProperties
     *   When true, values of promoted properties will be set on the
     *   \Pinto\Slots\Build object provided to the builder (usually __invoke) AFTER the builder method has run, and if
     *   the builder method did not already set a value for the slot.
     *   Must be false when $slots are provided.
     */
    public function __construct(
        string $useNamedParameters = self::useNamedParameters,
        $slots = [],
        public ?string $method = null,
        public bool $bindPromotedProperties = false,
    ) {
        if (self::useNamedParameters !== $useNamedParameters) {
            throw new \LogicException(self::useNamedParameters);
        }

        $this->slots = new SlotList();
        foreach ($slots as $slot) {
            if (is_string($slot) && \class_exists($slot)) {
                $r = new \ReflectionClass($slot);
                if ($r->implementsInterface(\UnitEnum::class)) {
                    foreach ($slot::cases() as $case) {
                        $this->slots->add(new Slot(name: $case, origin: Origin\EnumCase::createFromEnum($case)));
                    }
                }

                // Otherwise skip if it's a regular class name.
                continue;
            }
            $this->slots->add(
                $slot instanceof Slot
                  ? $slot
                  : new Slot(name: $slot, origin: match (true) {
                      \is_string($slot) => Origin\StaticallyDefined::create(data: $slot),
                      $slot instanceof \UnitEnum => Origin\EnumCase::createFromEnum($slot),
                  }),
            );
        }
    }

    public static function lateBindObjectToBuild(mixed $build, mixed $definition, object $object, LateBindObjectContext $context): void
    {
        assert($build instanceof Build);
        assert($definition instanceof Definition);

        foreach ($definition->slots as $slot) {
            if (true === $build->pintoHas($slot->name)) {
                continue;
            }

            $classProperty = $slot->fillValueFromThemeObjectClassPropertyWhenEmpty;
            if (null !== $classProperty) {
                // @phpstan-ignore-next-line
                $build->set($slot->name, $object->{$classProperty});
            }
        }

        // Auto-invoke known nested objects:
        foreach ($definition->slots as $slot) {
            if (true === $build->pintoHas($slot->name)) {
                $slotValue = $build->pintoGet($slot->name);
                if (\is_object($slotValue) && ($invokerMethod = $context->getBuildInvoker($slotValue::class)) !== null) {
                    // @phpstan-ignore-next-line
                    $build->set($slot->name, $slotValue->{$invokerMethod}());
                }
            }
        }
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
        $slots = $this->slots;

        // When no slots were provided to the attribute, use reflection:
        if (0 === $this->slots->count()) {
            $reflectionMethod = match (true) {
                $r instanceof \ReflectionClass && null !== $this->method => $r->getMethod($this->method),
                $r instanceof \ReflectionClass => $r->getConstructor() ?? throw new PintoThemeDefinition('Missing method to reflect parameters from.'),
                $r instanceof \ReflectionMethod => $r,
                default => throw new \LogicException('Unsupported reflection: ' . $r::class),
            };

            // Method doesn't need to be public if there are defined slots:
            if (true !== $reflectionMethod->isPublic()) {
                throw new PintoThemeDefinition(sprintf('Method %s::%s() must be public to be used as a %s entrypoint.', $reflectionMethod->getDeclaringClass()->getName(), $reflectionMethod->getShortName(), static::class));
            }

            $slots = new SlotList();
            $parametersFrom = false === $this->bindPromotedProperties ? $reflectionMethod : ($reflectionMethod->getDeclaringClass()->getConstructor() ?? throw new \LogicException('A constructor must be defined to use `bindPromotedProperties`'));
            foreach ($parametersFrom->getParameters() as $rParam) {
                $paramType = $rParam->getType();
                if ($paramType instanceof \ReflectionNamedType || $paramType instanceof \ReflectionUnionType) {
                    $args = ['name' => $rParam->getName()];
                    // Default should only be set if there is a default.
                    if ($rParam->isDefaultValueAvailable()) {
                        $args['defaultValue'] = $rParam->getDefaultValue();
                    }

                    if ($this->bindPromotedProperties && $rParam->isPromoted() && $reflectionMethod->getDeclaringClass()->getProperty($rParam->name)->isPublic()) {
                        $args['fillValueFromThemeObjectClassPropertyWhenEmpty'] = $rParam->name;
                    }

                    $args['origin'] = Origin\Parameter::fromReflection($rParam);

                    $slots[] = new Slot(...$args);
                }
            }
        } elseif (true === $this->bindPromotedProperties) {
            // Slots > 0 and bind properties are an invalid state.
            throw new PintoThemeDefinition(sprintf('Slots must use reflection (no explicitly defined `$slots`) when promoted properties bind is on.'));
        }

        // Now look for other attributes.

        $reflectionClass = match (true) {
            $r instanceof \ReflectionClass => $r,
            $r instanceof \ReflectionMethod => $r->getDeclaringClass(),
            default => throw new \LogicException('Unsupported reflection: ' . $r::class),
        };

        // Get the theme object class name.
        $rCase = new \ReflectionEnumUnitCase($case::class, $case->name);
        /** @var array<\ReflectionAttribute<\Pinto\Attribute\Definition>> $attributes */
        $attributes = $rCase->getAttributes(\Pinto\Attribute\Definition::class);
        $definition = ($attributes[0] ?? null)?->newInstance() ?? throw new \LogicException('Missing definition for slot');
        $objectClassName = $definition->className;

        $renameSlots = RenameSlots::create();

        // Only if the object type isn't defined on the same object:
        if ($reflectionClass->getName() !== $objectClassName) {
            $objectClassReflection = new \ReflectionClass($objectClassName);

            // Only check on the object class (not enums or parents, for now).
            foreach ($objectClassReflection->getAttributes(RenameSlot::class) as $rAttr) {
                $renameSlots->add($rAttr->newInstance());
            }

            foreach ($objectClassReflection->getAttributes(ModifySlots::class) as $rAttr) {
                $modifySlotsAttr = $rAttr->newInstance();
                foreach ($modifySlotsAttr->add as $add) {
                    $slots[] = $add;
                }
            }
        }

        return new Definition(
            slots: $slots,
            renameSlots: $renameSlots,
        );
    }
}
