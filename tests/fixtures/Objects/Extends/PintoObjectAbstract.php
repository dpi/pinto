<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Extends;

use Pinto\Attribute\ThemeDefinition;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\tests\fixtures\Lists\PintoListExtends;
use Pinto\ThemeDefinition\HookThemeDefinition;

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
                PintoObjectExtends1::class => [PintoListExtends::class, PintoListExtends::Extends1->name],
                PintoObjectExtends2::class => [PintoListExtends::class, PintoListExtends::Extends2->name],
            ],
            definitions: [
                PintoObjectExtends1::class => new HookThemeDefinition([]),
                PintoObjectExtends2::class => new HookThemeDefinition([]),
            ],
            buildInvokers: [
                PintoObjectExtends1::class => '__invoke',
                PintoObjectExtends2::class => '__invoke',
            ],
            types: [static::class => ThemeDefinition::class],
        );
    }
}
