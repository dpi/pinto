<?php

declare(strict_types=1);

namespace Pinto\Attribute\Asset;

/**
 * An attribute representing a single Javascript file asset used by an object.
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
final class Js implements JsAssetInterface, LocalAssetInterface
{
    private string $assetPath;

    /**
     * @var array<string, mixed>
     */
    public array $attributes = [];

    /**
     * Defines a Javascript asset for the built library.
     *
     * @param string $path
     *   A path to append after ObjectListInterface::cssDirectory for the enum
     *   this object is represented by
     */
    public function __construct(
        public string $path,
        public bool $minified = false,
        public bool $preprocess = false,
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

    public function getLibraryPath(): array
    {
        return ['js', $this->assetPath . '/' . $this->path];
    }
}
