<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Faulty;

use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;

final class PintoObjectZeroObjectTypeAttributes
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
        return new PintoMapping([], [], [], [], [], []);
    }
}
