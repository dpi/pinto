<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Slots;

use Pinto\Attribute\ObjectType;
use Pinto\List\Resource\ObjectListEnumResource;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\Slots\Build;
use Pinto\tests\fixtures\Lists\PintoListSlots;

/**
 * Where slot is missing a value.
 */
#[ObjectType\Slots]
final class PintoObjectSlotsMissingSlotValue
{
    use ObjectTrait;

    public function __construct(
        public readonly string $text,
        public readonly int $number,
    ) {
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Build $build): Build {
            return $build
              ->set('text', $this->text)
              // `number` is not set.
            ;
        });
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping(
            resources: [
                static::class => ObjectListEnumResource::createFromEnum(PintoListSlots::SlotMissingValue),
            ],
            definitions: [
                static::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'text', defaultValue: null),
                    new Slots\Slot(name: 'number'),
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
