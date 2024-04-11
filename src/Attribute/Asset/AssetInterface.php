<?php

declare(strict_types=1);

namespace Pinto\Attribute\Asset;

interface AssetInterface
{
    /**
     * @return string[]
     */
    public function getLibraryPath(): array;
}
