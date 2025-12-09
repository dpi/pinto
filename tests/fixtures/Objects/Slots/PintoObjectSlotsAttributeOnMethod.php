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
 * Slots derived from a specific method.
 */
final class PintoObjectSlotsAttributeOnMethod
{
    use ObjectTrait;

    private function __construct()
    {
    }

    /**
     * @phpstan-param array<mixed>|null $arr
     */
    #[ObjectType\Slots]
    public function create(
        ?string $foo = null,
        ?array $arr = [],
    ): void {
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Build $build): Build {
            return $build
              ->set('foo', '')
              ->set('arr', '')
            ;
        });
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping(
            resources: [
                static::class => ObjectListEnumResource::createFromEnum(PintoListSlots::Slots),
            ],
            definitions: [
                static::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'foo', defaultValue: null),
                    new Slots\Slot(name: 'arr', defaultValue: []),
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
