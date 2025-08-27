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
 * Where slot is set to the wrong PHP type.
 */
#[ObjectType\Slots]
final class PintoObjectSlotsValidationFailurePhpType
{
    use ObjectTrait;

    public function __construct(
        public readonly int $number,
    ) {
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Build $build): Build {
            return $build
              ->set('number', 'Foo')
            ;
        });
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping(
            enumClasses: [],
            enums: [
                static::class => [PintoListSlots::class, PintoListSlots::PintoObjectSlotsValidationFailurePhpType->name],
            ],
            definitions: [
                static::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'number', defaultValue: null, validation: Slots\Validation\PhpType::fromString('int')),
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
