<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Slots;

use Pinto\Attribute\ObjectType;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\Slots\Build;
use Pinto\tests\fixtures\Lists\PintoListSlots;

/**
 * Slots defined on the attribute.
 */
#[ObjectType\Slots(
    slots: [
        new Slots\Slot(name: 'text'),
        new Slots\Slot(name: 'number', defaultValue: 3),
    ],
)]
final class PintoObjectSlotsExplicit
{
    use ObjectTrait;

    public function __construct()
    {
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Build $build): Build {
            return $build
              ->set('text', 'Some text')
              ->set('number', 12345)
            ;
        });
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping(
            enumClasses: [],
            enums: [
                static::class => [PintoListSlots::class, PintoListSlots::Slots->name],
            ],
            definitions: [
                static::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'text'),
                    new Slots\Slot(name: 'number', defaultValue: 3),
                ])),
            ],
            buildInvokers: [
                static::class => '__invoke',
            ],
            types: [static::class => ObjectType\Slots::class],
        );
    }
}
