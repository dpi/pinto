<?php

declare(strict_types=1);

namespace Pinto\Slots\Origin;

/**
 * @internal
 */
final class EnumCase
{
    private function __construct(
        private readonly string $className,
        private readonly string $case,
    ) {
    }

    public static function createFromEnum(\UnitEnum $unitEnum): static
    {
        return new static(
            className: $unitEnum::class,
            case: $unitEnum->name
        );
    }

    public function enumCase(): \UnitEnum
    {
        try {
            // @phpstan-ignore return.type
            return \constant($this->className . '::' . $this->case);
        } catch (\Error) {
            throw new \InvalidArgumentException($this->className . '::' . $this->case . ' does not exist.');
        }
    }
}
