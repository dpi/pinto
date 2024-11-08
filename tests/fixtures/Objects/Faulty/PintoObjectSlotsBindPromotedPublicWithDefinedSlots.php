<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Faulty;

use Pinto\Attribute\ObjectType;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;

#[ObjectType\Slots(
    slots: [
        'test_slot',
    ],
    bindPromotedProperties: true,
)]
final class PintoObjectSlotsBindPromotedPublicWithDefinedSlots
{
    use ObjectTrait;

    private function __construct()
    {
    }

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
        return new PintoMapping([], [], [], [], []);
    }
}
