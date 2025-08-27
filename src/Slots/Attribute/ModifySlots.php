<?php

declare(strict_types=1);

namespace Pinto\Slots\Attribute;

use Pinto\Attribute\ObjectType\Slots;
use Pinto\Slots\Slot;
use Pinto\Slots\SlotList;

/**
 * An attribute for modifying the slots where slots were originally defined elsewhere.
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class ModifySlots
{
    private const useNamedParameters = 'Using this attribute without named parameters is not supported.';
    public SlotList $add;

    /**
     * Constructs a ModifySlots attribute.
     *
     * @param list<Slot|string|\UnitEnum|class-string<\UnitEnum>> $add
     *   The list of slots to append.
     *   Slots may be any mix of \Pinto\Slots\Slot objects, string, enums, or enum class-strings. Using string, enum,
     *   enum class-string slots are a simplified way of defining slots. To provide other options like default values,
     *   the \Pinto\Slots\Slot object or reflection (by omitting $slots) must be used.
     */
    public function __construct(
        string $useNamedParameters = self::useNamedParameters,
        $add = [],
    ) {
        if (self::useNamedParameters !== $useNamedParameters) {
            throw new \LogicException(self::useNamedParameters);
        }

        if ([] === $add) {
            throw new \LogicException('Slots must be added.');
        }

        // Recycle the logic from attribute
        $this->add = (new Slots(slots: $add))->slots;
    }
}
