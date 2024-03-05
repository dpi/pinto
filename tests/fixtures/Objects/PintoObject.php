<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects;

use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\Js;
use Pinto\Attribute\ThemeDefinition;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;

/**
 * Test object.
 */
#[Css('styles.css')]
#[Js('app.js')]
final class PintoObject
{
    use ObjectTrait;

    /**
     * Constructor.
     */
    private function __construct(
        readonly string $text,
    ) {
    }

    /**
     * Creates a new object.
     */
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

    /**
     * @return array<mixed>
     */
    #[ThemeDefinition]
    public static function theme(): array
    {
        return [
            'variables' => [
                'test_variable' => null,
            ],
        ];
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping(
            enumClasses: [],
            enums: [
              static::class => [PintoList::class, 'Pinto_Object'],
            ],
            themeDefinitions: [
              static::class => [],
            ],
            buildInvokers: [
              static::class => '__invoke',
            ],
        );
    }
}
