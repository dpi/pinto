<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Build;

use Pinto\Attribute\ThemeDefinition;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\tests\fixtures\Lists\PintoBuildOverrideList;
use Pinto\ThemeDefinition\HookThemeDefinition;

/**
 * Test where the list enum overrides the build of this object.
 *
 * @see PintoBuildOverrideList::build
 */
final class PintoBuildOverrideObject
{
    use ObjectTrait;

    /**
     * Constructor.
     */
    private function __construct(
        public readonly string $text,
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

    /**
     * @phpstan-return array{'build_context_from_list': class-string}
     */
    public function __invoke(): array
    {
        // @phpstan-ignore-next-line
        return $this->pintoBuild(function (mixed $build): array {
            return $build + [
                '#test_variable' => $this->text,
            ];
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
                static::class => [PintoBuildOverrideList::class, PintoBuildOverrideList::PintoBuildOverrideObject->name],
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
