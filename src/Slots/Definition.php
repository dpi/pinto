<?php

declare(strict_types=1);

namespace Pinto\Slots;

use Pinto\Slots\Attribute\RenameSlot;
use Pinto\CanonicalProduct\CanonicalProductDiscovery;

final class Definition
{
    public function __construct(
        public SlotList $slots,
        public ?RenameSlots $renameSlots = null,
    ) {
    }
}
