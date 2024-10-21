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
 * Where slot is missing a value, but a default is provided.
 */
#[ObjectType\Slots]
final class PintoObjectSlotsMissingSlotValueWithDefault
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
              // `number` is not set.
            ;
        });
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping(
            enumClasses: [],
            enums: [
                static::class => [PintoListSlots::class, PintoListSlots::PintoObjectSlotsMissingSlotValueWithDefault->name],
            ],
            definitions: [
                static::class => new Slots\Definition([
                    'text' => ['type' => 'string', 'default' => null],
                    'number' => ['type' => 'int', 'default' => 3],
                ]),
            ],
            buildInvokers: [
                static::class => '__invoke',
            ],
            types: [static::class => ObjectType\Slots::class],
        );
    }
}
