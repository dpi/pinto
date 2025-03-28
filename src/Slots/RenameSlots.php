<?php

declare(strict_types=1);

namespace Pinto\Slots;

use Pinto\Slots\Attribute\RenameSlot;
use Pinto\Exception\Slots\UnknownValue;

/**
 * @internal
 */
final class RenameSlots
{
    /**
     * @phpstan-param \SplObjectStorage<\UnitEnum, mixed> $slotObjValues
     * @phpstan-param array<string, mixed> $slotStringValues
     */
    private function __construct(
        // I'd like to use _new in initializers_ but PHPStan doesn't seem to have
        // a way of representing the generic types.
        private \SplObjectStorage $slotObjValues,
        private array $slotStringValues,
    ) {
      $this->slotObjValues = new \SplObjectStorage();
    }

    public static function create() {
      return new static(
        new \SplObjectStorage(),
        [],
      );
    }

    /**
     * @internal
     */
    public function renamesTo(string|\UnitEnum $slot): ?string
    {
        return \is_string($slot)
          ? (\array_key_exists($slot, $this->slotStringValues) ? $this->slotStringValues[$slot] : NULL)
          : $this->slotObjValues[$slot] ?? NULL
        ;
    }

    /**
     * @return $this
     */
    public function add(RenameSlot $renameSlot): static
    {
        if (\is_string($renameSlot->original)) {
            $this->slotStringValues[$renameSlot->original] = $renameSlot->new;
        } else {
            $this->slotObjValues[$renameSlot->original] = $renameSlot->new;
        }

        return $this;
    }
}
