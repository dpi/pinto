<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Build;

use Pinto\Attribute\Build;
use Pinto\Attribute\ObjectType\Slots;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;

/**
 * Test where build method is custom.
 */
#[Slots]
final class PintoBuildMethodObject
{
    use ObjectTrait;

    #[Build]
    public function builder(): void
    {
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping([], [], [], [], [], []);
    }
}
