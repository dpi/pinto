<?php

declare(strict_types=1);

namespace Pinto\Attribute\Asset;

use Pinto\Asset\AssetLibraryPaths;

/**
 * An attribute representing a single CSS file asset used by an object.
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
final class ExternalCss implements JsAssetInterface, ExternalAssetInterface
{
    /**
     * Defines an externally hosted Javascript asset.
     *
     * @phpstan-param array<string, mixed> $attributes
     */
    public function __construct(
        private readonly string $url,
        private string $category = 'component',
        public readonly bool $external = true,
        public readonly array $attributes = [],
    ) {
        if (!str_starts_with($url, 'https://') && !str_starts_with($url, '//')) {
            throw new \InvalidArgumentException('Invalid URL.');
        }
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLibraryPaths(): AssetLibraryPaths
    {
        return new AssetLibraryPaths([['css', $this->category, $this->url]]);
    }
}
