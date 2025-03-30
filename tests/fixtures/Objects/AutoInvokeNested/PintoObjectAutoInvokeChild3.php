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
    'child3_text',
])]
class PintoObjectAutoInvokeChild3
{
    use ObjectTrait;

    final public function __construct()
    {
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Slots\Build $build): Slots\Build {
            return $build
              ->set('child3_text', 'Text in Child3');
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
