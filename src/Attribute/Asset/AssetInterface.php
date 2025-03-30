<?php

declare(strict_types=1);

namespace Pinto\Attribute\Asset;

use Pinto\Asset\AssetLibraryPaths;

interface AssetInterface
{
    /**
     * Get a collection of library paths.
     *
     * @internal
     */
    public function getLibraryPaths(): AssetLibraryPaths;
}
