<?php

declare(strict_types=1);

namespace Pinto\Slots;

final class Slot
{
    private const useNamedParameters = 'Using this attribute without named parameters is not supported.';

    public function __construct(
        public readonly \UnitEnum|string $name,
        string $useNamedParameters = self::useNamedParameters,
        public readonly mixed $defaultValue = new NoDefaultValue(),
    ) {
        if (self::useNamedParameters !== $useNamedParameters) {
            throw new \LogicException(self::useNamedParameters);
        }
    }
}
