<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Extends;

use Pinto\Attribute\ObjectType;
use Pinto\List\Resource\ObjectListEnumResource;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\tests\fixtures\Lists\PintoListExtends;

abstract class PintoObjectAbstract
{
    use ObjectTrait;

    final private function __construct(
        public readonly string $text,
    ) {
    }

    public static function create(
        string $text,
    ): static {
        return new static($text);
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Slots\Build $build): Slots\Build {
            return $build->set('text', $this->text);
        });
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping(
            resources: [
                PintoObjectExtends1::class => ObjectListEnumResource::createFromEnum(PintoListExtends::Extends1),
                PintoObjectExtends2::class => ObjectListEnumResource::createFromEnum(PintoListExtends::Extends2),
            ],
            definitions: [
                PintoObjectExtends1::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'text'),
                ])),
                PintoObjectExtends2::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'text'),
                ])),
            ],
            buildInvokers: [
                PintoObjectExtends1::class => '__invoke',
                PintoObjectExtends2::class => '__invoke',
            ],
            types: [static::class => ObjectType\Slots::class],
            lsbFactoryCanonicalObjectClasses: [],
        );
    }
}
