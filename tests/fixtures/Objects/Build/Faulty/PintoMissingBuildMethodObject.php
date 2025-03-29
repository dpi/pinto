<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Build\Faulty;

use Pinto\Attribute\ObjectType\Slots;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;

/**
 * Test where no build method is specific and no invoker.
 */
#[Slots]
final class PintoMissingBuildMethodObject
{
    use ObjectTrait;

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping([], [], [], [], [], []);
    }
}
