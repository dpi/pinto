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
     * @phpstan-param \SplObjectStorage<\UnitEnum, string> $enumSlotRenames
     * @phpstan-param array<string, string> $stringSlotRenames
     */
    private function __construct(
        // I'd like to use _new in initializers_ but PHPStan doesn't seem to have
        // a way of representing the generic types.
        private \SplObjectStorage $enumSlotRenames,
        private array $stringSlotRenames,
    ) {
    }

    public static function create(): static {
      /** @var \SplObjectStorage<\UnitEnum, string> $enumSlotRenames */
      $enumSlotRenames = new \SplObjectStorage();
      return new static(
        $enumSlotRenames,
        [],
      );
    }

    /**
     * @internal
     */
    public function renamesTo(string|\UnitEnum $slot): ?string
    {
        return \is_string($slot)
          ? (\array_key_exists($slot, $this->stringSlotRenames) ? $this->stringSlotRenames[$slot] : NULL)
          : $this->enumSlotRenames[$slot] ?? NULL
        ;
    }

    /**
     * @return $this
     */
    public function add(RenameSlot $renameSlot): static
    {
        if (\is_string($renameSlot->original)) {
            $this->stringSlotRenames[$renameSlot->original] = $renameSlot->new;
        } else {
            $this->enumSlotRenames[$renameSlot->original] = $renameSlot->new;
        }

        return $this;
    }
}
