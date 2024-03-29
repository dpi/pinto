<?php

declare(strict_types=1);

namespace Pinto\List;

use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\CssAssetInterface;
use Pinto\Attribute\Asset\Js;
use Pinto\Attribute\Asset\JsAssetInterface;
use Pinto\Attribute\Definition;
use Pinto\Attribute\ThemeDefinition;
use Pinto\Exception\PintoCaseMissingDefinitionAttribute;

/**
 * Implements interface defaults.
 *
 * @see ObjectListInterface
 */
trait ObjectListTrait
{
    public function name(): string
    {
        return match ($this) {
            default => $this->value,
        };
    }

    public function templateName(): string
    {
        return str_replace('_', '-', $this->name());
    }

    public function libraryName(): string
    {
        return match ($this) {
            default => $this->value,
        };
    }

    public function attachLibraries(): array
    {
        return [
          sprintf('pinto/%s', $this->libraryName()),
        ];
    }

    public function build(callable $wrapper, object $object): callable
    {
        return function (mixed $build) use ($wrapper) {
            // Override this trait fully, copy its contents, add your logic here.
            return $wrapper($build);
        };
    }

    public function assets(): iterable
    {
        $reflection = new \ReflectionEnumUnitCase($this::class, $this->name);
        $definition = ($reflection->getAttributes(Definition::class)[0] ?? null)?->newInstance() ?? throw new \LogicException('All component cases must have a `' . Definition::class . '`.');

        $reflectionComponentClass = new \ReflectionClass($definition->className);

        return array_map(fn (\ReflectionAttribute $r) => $r->newInstance(), [
          ...$reflectionComponentClass->getAttributes(Js::class),
          ...$reflectionComponentClass->getAttributes(Css::class),
        ]);
    }

    public static function themeDefinitions(array $existing, string $type, string $theme, string $path): array
    {
        return array_reduce(
            static::cases(),
            static function (array $carry, self $case): array {
                $reflection = new \ReflectionEnumUnitCase($case::class, $case->name);
                $definition = ($reflection->getAttributes(Definition::class)[0] ?? null)?->newInstance() ?? throw new PintoCaseMissingDefinitionAttribute($case::class, $case->name);

                $carry[$case->name()] = ThemeDefinition::themeDefinitionForThemeObject($definition->className) + [
                  'variables' => [],
                  'path' => $case->templateDirectory(),
                  'template' => $case->templateName(),
                ];

                return $carry;
            },
            [],
        );
    }

    /**
     * Get all library definitions for all components.
     *
     * @phpstan-return array<string, array{css?: array<string, array<string, array<mixed>>>, js?: array<string, array<mixed>>}>
     */
    public static function libraries(): array
    {
        $nestedValueSet = static function (&$array, $keys, $value) {
            $current = &$array;
            foreach ($keys as $key) {
                if (!isset($current[$key]) || !is_array($current[$key])) {
                    $current[$key] = [];
                }
                $current = &$current[$key];
            }
            $current = $value;
        };

        return array_reduce(
            static::cases(),
            static function (array $libraries, self $case) use ($nestedValueSet): array {
                $library = [];
                foreach ($case->assets() as $asset) {
                    /** @var \Pinto\Attribute\Asset\AssetInterface $asset */
                    /** @var (array{'path': string}&array<string, mixed>) $vars */
                    $vars = get_object_vars($asset);
                    unset($vars['path']);
                    if ($asset instanceof JsAssetInterface) {
                        $asset->setPath($case->jsDirectory());
                    } elseif ($asset instanceof CssAssetInterface) {
                        $asset->setPath($case->cssDirectory());
                    }

                    $nestedValueSet($library, $asset->getLibraryPath(), $vars);
                }

                // Define the library if there is at least one asset.
                if (count($library) > 0) {
                    $libraries[$case->libraryName()] = $library;
                }

                return $libraries;
            },
            [],
        );
    }
}
