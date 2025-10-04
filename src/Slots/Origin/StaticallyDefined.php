<?php

declare(strict_types=1);

namespace Pinto\Slots\Origin;

/**
 * @internal
 */
final class StaticallyDefined
{
    public function __construct(
        private ?string $data = null,
    ) {
    }

    public static function create(?string $data = null): static
    {
        return new static(
            data: $data,
        );
    }

    public function data(): ?string
    {
        return $this->data;
    }
}
