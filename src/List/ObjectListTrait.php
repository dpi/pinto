<?php

declare(strict_types=1);

namespace Pinto\List;

use Pinto\Attribute\Asset\AssetInterface;
use Pinto\Attribute\Asset\CssAssetInterface;
use Pinto\Attribute\Asset\JsAssetInterface;
use Pinto\Attribute\Asset\LocalAssetInterface;
use Pinto\Attribute\Definition;
use Pinto\Attribute\DependencyOn;
use Pinto\DefinitionCollection;
use Pinto\DefinitionDiscovery;
use Pinto\ObjectType\ObjectTypeDiscovery;
use Pinto\PintoMapping;

/**
 * Implements interface defaults.
 *
 * @see ObjectListInterface
 *
 * @phpstan-require-implements \Pinto\List\ObjectListInterface
 */
trait ObjectListTrait
{
    public function name(): string
    {
        return match ($this) {
            default => $this instanceof \BackedEnum ? $this->value : $this->name,
        };
    }

    public function templateName(): string
    {
        return str_replace('_', '-', $this->name());
    }

    public function libraryName(): string
    {
        return match ($this) {
            default => $this instanceof \BackedEnum ? $this->value : $this->name,
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
        $rCase = new \ReflectionEnumUnitCase($this::class, $this->name);
        $definitionAttr = ($rCase->getAttributes(Definition::class)[0] ?? null)?->newInstance();
        $rComponentClass = null !== $definitionAttr ? new \ReflectionClass($definitionAttr->className) : null;

        /** @var array<\ReflectionAttribute<AssetInterface>> $assets */
        $assets = ($rComponentClass ?? $rCase)->getAttributes(AssetInterface::class, \ReflectionAttribute::IS_INSTANCEOF);

        // Else, try the enum if object or case does not have assets.
        if ([] === $assets) {
            $rEnum = new \ReflectionClass($this::class);
            $assets = $rEnum->getAttributes(AssetInterface::class, \ReflectionAttribute::IS_INSTANCEOF);
        }

        return array_map(fn (\ReflectionAttribute $r) => $r->newInstance(), $assets);
    }

    public static function definitions(DefinitionDiscovery $definitionDiscovery): DefinitionCollection
    {
        $collection = new DefinitionCollection();

        foreach (static::cases() as $case) {
            $rCase = new \ReflectionEnumUnitCase($case::class, $case->name);
            $definitionAttr = ($rCase->getAttributes(Definition::class)[0] ?? null)?->newInstance();
            if (null !== $definitionAttr) {
                $collection[$case] = ObjectTypeDiscovery::definitionForThemeObject($definitionAttr->className, $case, $definitionDiscovery)[1];
            }
        }

        return $collection;
    }

    /**
     * Get all library definitions for all components.
     *
     * @phpstan-return array<string, array{css?: array<string, array<string, array<mixed>>>, js?: array<string, array<mixed>>}>
     */
    public static function libraries(PintoMapping $pintoMapping): array
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
            static function (array $libraries, self $case) use ($pintoMapping, $nestedValueSet): array {
                $library = [];
                foreach ($case->assets() as $asset) {
                    /** @var JsAssetInterface|CssAssetInterface $asset */
                    /** @var (array{'path': string}&array<string, mixed>) $vars */
                    $vars = get_object_vars($asset);
                    unset($vars['path']);
                    if ($asset instanceof LocalAssetInterface) {
                        if ($asset instanceof JsAssetInterface) {
                            //                            if (!\is_dir($case->jsDirectory())) {
                            //                                throw new \LogicException(sprintf('JS directory `%s` does not exist for `%s:%s`', $case->jsDirectory(), $case::class, $case->name));
                            //                            }

                            $asset->setPath($case->jsDirectory());
                        } elseif ($asset instanceof CssAssetInterface) {
                            //                            if (!\is_dir($case->cssDirectory())) {
                            //                                throw new \LogicException(sprintf('CSS directory `%s` does not exist for `%s:%s`', $case->cssDirectory(), $case::class, $case->name));
                            //                            }

                            $asset->setPath($case->cssDirectory());
                        }
                    }

                    foreach ($asset->getLibraryPaths() as $libraryPath) {
                        $nestedValueSet($library, $libraryPath, $vars);
                    }
                }

                $rEnum = new \ReflectionClass($case::class);
                $rCase = new \ReflectionEnumUnitCase($case::class, $case->name);
                foreach ([
                    ...$rEnum->getAttributes(DependencyOn::class),
                    ...$rCase->getAttributes(DependencyOn::class),
                ] as $r) {
                    $dependencyAttr = $r->newInstance();

                    $on = [];
                    if (null !== $dependencyAttr->dependency) {
                        $on = $dependencyAttr->dependency instanceof ObjectListInterface
                          ? $dependencyAttr->dependency->attachLibraries()
                          : [$dependencyAttr->dependency];
                    } elseif (true === $dependencyAttr->parent) {
                        $definitionAttr = ($rCase->getAttributes(Definition::class)[0] ?? null)?->newInstance();
                        if (null !== $definitionAttr) {
                            $factoryClass = $pintoMapping->getFactoryOfCanonicalObject($definitionAttr->className) ?? throw new \LogicException('Unable to determine parent of ' . $definitionAttr->className);
                            $factoryEnumCase = $pintoMapping->getByClass($factoryClass);
                            $on = $factoryEnumCase->attachLibraries();
                        }
                    }

                    $library['dependencies'] = [
                        ...($library['dependencies'] ?? []),
                        ...$on,
                    ];
                }

                // Define the library if there is at least one asset or dependency.
                if ([] !== $library) {
                    $libraries[$case->libraryName()] = $library;
                }

                return $libraries;
            },
            [],
        );
    }
}
