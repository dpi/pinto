<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects;

use Pinto\Attribute\ThemeDefinition;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;

/**
 * Test object.
 */
final class PintoBuildOverrideObject
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

    /**
     * @phpstan-return array{'build_context_from_list': class-string}
     */
    public function __invoke(): array
    {
        // @phpstan-ignore-next-line
        return $this->pintoBuild(function (mixed $build): array {
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
              static::class => [PintoBuildOverrideList::class, 'PintoBuildOverrideObject'],
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
