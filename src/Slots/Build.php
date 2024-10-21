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
     */
    private function __construct(
        // I'd like to use _new in initializers_ but PHPStan doesn't seem to have
        // a way of representing the generic types.
        private \SplObjectStorage $slotObjValues,
        private array $slotStringValues = [],
    ) {
    }

    /**
     * Construct a new slot build.
     */
    public static function create(): static
    {
        /** @var \SplObjectStorage<\UnitEnum, mixed> $slotObjValues */
        $slotObjValues = new \SplObjectStorage();

        return new static($slotObjValues, []);
    }

    /**
     * Get the value of a slot.
     *
     * @internal
     */
    public function pintoGet(string|\UnitEnum $slot): mixed
    {
        return \is_string($slot)
          ? \array_key_exists($slot, $this->slotStringValues) ? $this->slotStringValues[$slot] : throw new UnknownValue('Unknown slot `' . $slot . '`') : $this->slotObjValues[$slot] ?? throw new UnknownValue('Unknown slot `' . $slot->name . '`')
        ;
    }

    /**
     * Determine if a slot is filled.
     *
     * @internal
     */
    public function pintoHas(string|\UnitEnum $slot): mixed
    {
        return \is_string($slot) ? \array_key_exists($slot, $this->slotStringValues) : $this->slotObjValues[$slot];
    }

    /**
     * Set the value of a slot.
     *
     * @return $this
     */
    public function set(string|\UnitEnum $slot, mixed $value): static
    {
        if (\is_string($slot)) {
            $this->slotStringValues[$slot] = $value;
        } else {
            $this->slotObjValues[$slot] = $value;
        }

        return $this;
    }
}
