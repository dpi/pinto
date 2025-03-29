<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Faulty;

use Pinto\Attribute\ObjectType;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;

#[ObjectType\Slots]
final class PintoObjectMultipleObjectTypeAttributes
{
    use ObjectTrait;

    private function __construct()
    {
    }

    #[ObjectType\Slots]
    public static function create(): static
    {
        return new static();
    }

    public function __invoke(): mixed
    {
        return [];
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping([], [], [], [], [], []);
    }
}
