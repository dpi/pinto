<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Slots;

use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\tests\fixtures\Etc\SlotEnum;

/**
 * Rename slots from parent.
 */
#[Slots\Attribute\RenameSlot('stringFromParentThatWillBeRenamed', 'stringRenamed')]
#[Slots\Attribute\RenameSlot(SlotEnum::Slot1, 'enumRenamed')]
final class PintoObjectSlotsRenameChild extends PintoObjectSlotsRenameParent
{
    use ObjectTrait;

    public function __construct(
        public readonly string $fooFromChild,
    ) {
        parent::__construct();
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
