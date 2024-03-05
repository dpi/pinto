<?php

declare(strict_types=1);

namespace Pinto\Attribute\Asset;

interface AssetInterface
{
    /**
     * @return $this
     */
    public function setPath(string $assetPath);

    /**
     * @return string[]
     */
    public function getLibraryPath(): array;
}
