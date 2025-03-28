<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Slots;

use Pinto\Attribute\ObjectType;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\Slots\Build;
use Pinto\tests\fixtures\Lists\PintoListSlotsOnEnumMethodSpecified;

/**
 * Slots from list.
 */
final class PintoObjectSlotsFromListMethodSpecified
{
    use ObjectTrait;

    public function create(
        ?string $create = 'from method specified on enum #[Slots]',
    ): void {
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Build $build): Build {
            return $build
              ->set('create', '')
            ;
        });
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping(
            enumClasses: [],
            enums: [
                static::class => [PintoListSlotsOnEnumMethodSpecified::class, PintoListSlotsOnEnumMethodSpecified::SlotsOnEnumMethodSpecified->name],
            ],
            definitions: [
                static::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'create', defaultValue: 'from method specified on enum #[Slots]'),
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
