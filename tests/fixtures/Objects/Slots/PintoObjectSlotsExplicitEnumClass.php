<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Slots;

use Pinto\Attribute\ObjectType;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\Slots\Build;
use Pinto\tests\fixtures\Etc\SlotEnum;
use Pinto\tests\fixtures\Lists\PintoListSlots;

/**
 * Slots defined on the attribute, where slot names are enums.
 */
#[ObjectType\Slots(
    slots: [
        SlotEnum::class,
    ],
)]
final class PintoObjectSlotsExplicitEnumClass
{
    use ObjectTrait;

    public function __construct()
    {
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Build $build): Build {
            return $build
              ->set(SlotEnum::Slot1, 'Slot One')
              ->set(SlotEnum::Slot2, 'Slot Two')
              ->set(SlotEnum::Slot3, 'Slot Three')
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
                    new Slots\Slot(name: SlotEnum::Slot1),
                    new Slots\Slot(name: SlotEnum::Slot2),
                    new Slots\Slot(name: SlotEnum::Slot3),
                ])),
            ],
            buildInvokers: [
                static::class => '__invoke',
            ],
            types: [static::class => ObjectType\Slots::class],
        );
    }
}
