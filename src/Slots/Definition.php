<?php

declare(strict_types=1);

namespace Pinto\Slots;

final class Definition
{
    public function __construct(
        public SlotList $slots,
        public ?RenameSlots $renameSlots = null,
    ) {
    }
}
