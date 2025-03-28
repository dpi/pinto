<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Slots;

use Pinto\Attribute\ObjectType;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\tests\fixtures\Etc\SlotEnum;

/**
 * Rename slots from parent.
 */
#[ObjectType\Slots(slots: [
    'slotFromParentUnrenamed',
    'stringFromParentThatWillBeRenamed',
    SlotEnum::Slot1,
])]
class PintoObjectSlotsRenameParent
{
    use ObjectTrait;

    public function __construct()
    {
    }

    public function __invoke(): mixed
    {
        throw new \LogicException('Object level logic not tested.');
    }

    private function pintoMapping(): PintoMapping
    {
        throw new \LogicException('Object level logic not tested.');
    }
}
