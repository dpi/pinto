<?php

declare(strict_types=1);

namespace Pinto\Attribute\Asset;

/**
 * An attribute representing a single Javascript file asset used by an object.
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
final class ExternalJs implements JsAssetInterface, ExternalAssetInterface
{
    /**
     * Defines an externally hosted Javascript asset.
     *
     * @phpstan-param array<string, mixed> $attributes
     */
    public function __construct(
        private readonly string $url,
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

    public function getLibraryPath(): array
    {
        return ['js', $this->url];
    }
}
