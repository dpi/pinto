<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Extends;

use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\tests\fixtures\Lists\PintoListExtends;

abstract class PintoObjectAbstract
{
    use ObjectTrait;

    final private function __construct(
        readonly string $text,
    ) {
    }

    public static function create(
        string $text,
    ): static {
        return new static($text);
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (mixed $build): mixed {
            return $build + [];
        });
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping(
            enumClasses: [],
            enums: [
                PintoObjectExtends1::class => [PintoListExtends::class, 'Extends1'],
                PintoObjectExtends2::class => [PintoListExtends::class, 'Extends2'],
            ],
            themeDefinitions: [
                PintoObjectExtends1::class => [],
                PintoObjectExtends2::class => [],
            ],
            buildInvokers: [
                PintoObjectExtends1::class => '__invoke',
                PintoObjectExtends2::class => '__invoke',
            ],
        );
    }
}
