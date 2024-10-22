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
 * Slots basic.
 */
#[ObjectType\Slots]
final class PintoObjectSlotsBasic
{
    use ObjectTrait;

    public function __construct(
        readonly string $text,
        readonly int $number = 3,
    ) {
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Build $build): Build {
            return $build
              ->set('text', $this->text)
              ->set('number', $this->number)
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
                    new Slots\Slot(name: 'text', defaultValue: null),
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
