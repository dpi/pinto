<?php

declare(strict_types=1);

namespace Pinto\Attribute\Asset;

use Pinto\Asset\AssetLibraryPaths;

/**
 * An attribute representing a single Javascript file asset used by an object.
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
final class Js implements JsAssetInterface, LocalAssetInterface
{
    private string $assetPath;

    /**
     * Defines a Javascript asset for the built library.
     *
     * @param string $path
     *   A path to append after ObjectListInterface::jsDirectory for the enum
     *   this object is represented by
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        public string $path = '*.js',
        public bool $minified = false,
        public bool $preprocess = false,
        public readonly array $attributes = [],
    ) {
        if (str_starts_with($path, '/')) {
            throw new \LogicException('Path must not begin with forward-slash');
        }
    }

    public function setPath(string $assetPath)
    {
        $this->assetPath = $assetPath;

        return $this;
    }

    public function getLibraryPaths(): AssetLibraryPaths
    {
        $pattern = $this->assetPath . '/' . $this->path;
        $glob = \glob($pattern);
        if (false === $glob || [] === $glob) {
            if (!\file_exists($pattern)) {
                // No exceptions when globs are used, no files are allowed.
                throw new \LogicException('File does not exist: ' . $pattern);
            }

            return new AssetLibraryPaths([['js', $pattern]]);
        }

        $paths = new AssetLibraryPaths();
        foreach ($glob as $path) {
            $paths[] = ['js', $path];
        }

        return $paths;
    }
}
