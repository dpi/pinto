<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\AutoInvokeNested;

use Pinto\Attribute\ObjectType;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\tests\fixtures\Lists\AutoInvokeNested\PintoListAutoInvokeNested;

/**
 * PintoListAutoInvokeNested test object.
 */
#[ObjectType\Slots(slots: [
    'child1_text',
])]
class PintoObjectAutoInvokeChild1
{
    use ObjectTrait;

    final public function __construct()
    {
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Slots\Build $build): Slots\Build {
            return $build
              ->set('child1_text', 'Text in Child1');
        });
    }

    private static function pintoMappingStatic(): PintoMapping
    {
        return PintoObjectAutoInvokeContainer::pintoMappingStatic();
    }

    private function pintoMapping(): PintoMapping
    {
        return self::pintoMappingStatic();
    }
}
