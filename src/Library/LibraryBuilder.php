<?php

declare(strict_types=1);

namespace Pinto\Library;

use Pinto\Attribute\Asset\CssAssetInterface;
use Pinto\Attribute\Asset\JsAssetInterface;
use Pinto\Attribute\Asset\LocalAssetInterface;
use Pinto\List\Resource\ObjectListEnumResource;
use Pinto\PintoMapping;
use Pinto\Resource\ResourceInterface;

/**
 * @internal
 */
final class LibraryBuilder
{
    /**
     * @internal
     *
     * @return \Generator<array{JsAssetInterface|CssAssetInterface, string[]}>
     */
    public static function expandLibraryPaths(ResourceInterface $resource): \Generator
    {
        foreach ($resource->assets() as $asset) {
            /** @var JsAssetInterface|CssAssetInterface $asset */
            if ($asset instanceof LocalAssetInterface) {
                if ($asset instanceof JsAssetInterface) {
                    $asset->setPath($resource->jsDirectory());
                } elseif ($asset instanceof CssAssetInterface) {
                    $asset->setPath($resource->cssDirectory());
                }
            }

            foreach ($asset->getLibraryPaths() as $libraryPath) {
                yield [$asset, $libraryPath];
            }
        }
    }

    /**
     * Solves dependencies to known discovered resources.
     *
     * @internal
     */
    public static function solveDeps(ResourceInterface $resource, PintoMapping $pintoMapping): DependencyCollection
    {
        $deps = [];

        // Compute what DependencyOn instances are referencing.
        foreach ($resource->dependencies() as $dependencyEnum) {
            if (null !== $dependencyEnum->dependency) {
                $deps[] = $dependencyEnum->dependency;
            } elseif (true === $dependencyEnum->parent) {
                $objectClassName = $resource->getClass();
                if (null !== $objectClassName) {
                    $factoryClass = $pintoMapping->getFactoryOfCanonicalObject($objectClassName) ?? throw new \LogicException('Unable to determine parent of ' . $objectClassName);
                    $deps[] = $pintoMapping->getResource($factoryClass);
                }
            }
        }

        if ([] === $deps) {
            return DependencyCollection::create([]);
        }

        // Replace dependency enum references with their resource reference.
        foreach ($pintoMapping->getResources() as $knownResource) {
            if ($knownResource instanceof ObjectListEnumResource) {
                if (false !== ($k = \array_search($knownResource->pintoEnum, $deps, true))) {
                    $deps[$k] = $knownResource;
                }
            }
        }

        return DependencyCollection::create(dependencies: $deps);
    }
}
