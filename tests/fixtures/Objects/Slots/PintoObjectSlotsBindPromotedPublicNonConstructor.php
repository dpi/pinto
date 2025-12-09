<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Slots;

use Pinto\Attribute\ObjectType;
use Pinto\List\Resource\ObjectListEnumResource;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\Slots\NoDefaultValue;
use Pinto\tests\fixtures\Lists\PintoListSlots;

/**
 * Bind from promoted properties while #[Slots] is on non-constructor.
 */
final class PintoObjectSlotsBindPromotedPublicNonConstructor
{
    use ObjectTrait;

    private function __construct(
        public readonly string $foo,
        public readonly string $bar,
    ) {
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Slots\Build $build): Slots\Build {
            return $build
              ->set('foo', 'FOO!')
              ->set('bar', 'BAR!')
            ;
        });
    }

    #[ObjectType\Slots(bindPromotedProperties: true)]
    public static function actualEntrypoint(
        string $these,
        string $must,
        string $not,
        string $turn,
        string $into,
        string $slots,
    ): static {
        return new static('foo', 'bar');
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping(
            resources: [
                static::class => ObjectListEnumResource::createFromEnum(PintoListSlots::PintoObjectSlotsBindPromotedPublicNonConstructor),
            ],
            definitions: [
                static::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'foo', defaultValue: new NoDefaultValue(), fillValueFromThemeObjectClassPropertyWhenEmpty: 'foo'),
                    new Slots\Slot(name: 'bar', defaultValue: new NoDefaultValue(), fillValueFromThemeObjectClassPropertyWhenEmpty: 'bar'),
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
