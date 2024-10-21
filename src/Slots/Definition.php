<?php

declare(strict_types=1);

namespace Pinto\Slots;

final class Definition
{
    /**
     * @param array<string, array{type: string, default?: mixed}> $slots
     */
    public function __construct(
        // @todo adapt to Enum-keys.
        public array $slots,
    ) {
    }
}
