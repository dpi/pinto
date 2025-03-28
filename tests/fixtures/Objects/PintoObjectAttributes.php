<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects;

use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\ExternalCss;
use Pinto\Attribute\Asset\ExternalJs;
use Pinto\Attribute\Asset\Js;
use Pinto\Attribute\ThemeDefinition;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\tests\fixtures\Lists\PintoList;
use Pinto\ThemeDefinition\HookThemeDefinition;

/**
 * Test object.
 */
#[Css('styles.css', attributes: ['defer' => true])]
#[Js('app.js', attributes: ['defer' => true])]
#[ExternalJs('https://example.com/path.js', attributes: ['defer' => true])]
#[ExternalCss('https://example.com/path.css', attributes: ['defer' => true])]
final class PintoObjectAttributes
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
                static::class => [PintoList::class, PintoList::Pinto_Object_Attributes->name],
            ],
            definitions: [
                static::class => new HookThemeDefinition([
                    'variables' => [
                        'test_variable' => null,
                    ],
                ]),
            ],
            buildInvokers: [
                static::class => '__invoke',
            ],
            types: [static::class => ThemeDefinition::class],
            lsbFactoryCanonicalObjectClasses: [],
        );
    }
}
