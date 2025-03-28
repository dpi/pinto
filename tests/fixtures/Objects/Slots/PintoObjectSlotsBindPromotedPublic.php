<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Slots;

use Pinto\Attribute\ObjectType;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\Slots\NoDefaultValue;
use Pinto\tests\fixtures\Lists\PintoListSlots;

/**
 * Bind from promoted properties.
 */
#[ObjectType\Slots(bindPromotedProperties: true)]
final class PintoObjectSlotsBindPromotedPublic
{
    use ObjectTrait;

    public function __construct(
        public readonly string $aPublic,
        public readonly string $aPublicAndSetInInvoker,
        // @phpstan-ignore-next-line
        private readonly string $aPrivate,
    ) {
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Slots\Build $build): Slots\Build {
            return $build
              ->set('aPublicAndSetInInvoker', 'public value set in invoker')
              ->set('aPrivate', 'private value set in invoker');
        });
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping(
            enumClasses: [],
            enums: [
                static::class => [PintoListSlots::class, PintoListSlots::PintoObjectSlotsBindPromotedPublic->name],
            ],
            definitions: [
                static::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'aPublic', defaultValue: new NoDefaultValue(), fillValueFromThemeObjectClassPropertyWhenEmpty: 'aPublic'),
                    new Slots\Slot(name: 'aPublicAndSetInInvoker', defaultValue: new NoDefaultValue(), fillValueFromThemeObjectClassPropertyWhenEmpty: 'aPublicAndSetInInvoker'),
                    new Slots\Slot(name: 'aPrivate', defaultValue: new NoDefaultValue(), fillValueFromThemeObjectClassPropertyWhenEmpty: null),
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
