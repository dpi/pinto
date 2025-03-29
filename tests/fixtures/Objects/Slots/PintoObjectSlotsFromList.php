<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Slots;

use Pinto\Attribute\ObjectType;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\Slots\Build;
use Pinto\tests\fixtures\Lists\PintoListSlotsOnEnum;

/**
 * Slots from list.
 */
final class PintoObjectSlotsFromList
{
    use ObjectTrait;

    public function __construct(
        readonly string $fooFromList,
        readonly int $number = 4,
    ) {
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Build $build): Build {
            return $build
              ->set('fooFromList', $this->fooFromList)
              ->set('number', $this->number)
            ;
        });
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping(
            enumClasses: [],
            enums: [
                static::class => [PintoListSlotsOnEnum::class, PintoListSlotsOnEnum::SlotsOnEnum->name],
            ],
            definitions: [
                static::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'fooFromList', defaultValue: null),
                    new Slots\Slot(name: 'number', defaultValue: 4),
                ])),
            ],
            buildInvokers: [
                static::class => '__invoke',
            ],
            types: [static::class => ObjectType\Slots::class],
            lsbFactoryCanonicalObjectClasses: [],
        );
    }
}
