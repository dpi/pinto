<?php

declare(strict_types=1);

namespace Pinto\Slots;

use Pinto\Exception\Slots\UnknownValue;

/**
 * This value object must be returned by theme objects implementing #[Slots].
 *
 * The form of this object may change in the future.
 */
final class Build
{
    /**
     * @phpstan-param \SplObjectStorage<\UnitEnum, mixed> $slotObjValues
     * @phpstan-param array<string, mixed> $slotStringValues
     * @phpstan-param list<string|\UnitEnum> $slotNames
     */
    private function __construct(
        // I'd like to use _new in initializers_ but PHPStan doesn't seem to have
        // a way of representing the generic types.
        private \SplObjectStorage $slotObjValues,
        private array $slotStringValues = [],
        private array $slotNames = [],
    ) {
    }

    /**
     * Construct a new slot build.
     *
     * @internal
     */
    public static function create(SlotList $slotList): static
    {
        /** @var \SplObjectStorage<\UnitEnum, mixed> $slotObjValues */
        $slotObjValues = new \SplObjectStorage();
        $slotNames = \array_map(fn (Slot $slot): \UnitEnum|string => $slot->name, $slotList->toArray());

        return new static($slotObjValues, [], $slotNames);
    }

    /**
     * Get the value of a slot.
     *
     * @internal
     */
    public function pintoGet(string|\UnitEnum $slot): mixed
    {
        if (!\in_array($slot, $this->slotNames, true)) {
            throw new UnknownValue(sprintf('Unknown slot `%s`', \is_string($slot) ? $slot : sprintf('%s::%s', $slot::class, $slot->name)));
        }

        return \is_string($slot)
          ? (\array_key_exists($slot, $this->slotStringValues) ? $this->slotStringValues[$slot] : throw new UnknownValue('Value not set for slot `' . $slot . '`'))
          : ($this->slotObjValues->offsetExists($slot) ? $this->slotObjValues[$slot] : throw new UnknownValue(sprintf('Value not set for slot `%s::%s`', $slot::class, $slot->name)))
        ;
    }

    /**
     * Determine if a slot is filled.
     *
     * @internal
     */
    public function pintoHas(string|\UnitEnum $slot): mixed
    {
        return \is_string($slot) ? \array_key_exists($slot, $this->slotStringValues) : $this->slotObjValues->offsetExists($slot);
    }

    /**
     * Set the value of a slot.
     *
     * @return $this
     */
    public function set(string|\UnitEnum $slot, mixed $value): static
    {
        if (!\in_array($slot, $this->slotNames, true)) {
            throw new UnknownValue(sprintf('Unknown slot `%s`', \is_string($slot) ? $slot : sprintf('%s::%s', $slot::class, $slot->name)));
        }

        if (\is_string($slot)) {
            $this->slotStringValues[$slot] = $value;
        } else {
            $this->slotObjValues[$slot] = $value;
        }

        return $this;
    }
}
