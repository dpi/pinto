<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Slots;

use Pinto\Attribute\ObjectType;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;

/**
 * Slots with no constructor.
 */
#[ObjectType\Slots(slots: ['foo'])]
final class PintoObjectSlotsNoConstructor
{
    use ObjectTrait;

    public function __invoke(): mixed
    {
        throw new \LogicException('Object level logic not tested.');
    }

    private function pintoMapping(): PintoMapping
    {
        throw new \LogicException('Object level logic not tested.');
    }
}
