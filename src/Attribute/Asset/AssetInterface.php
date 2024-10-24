<?php

declare(strict_types=1);

namespace Pinto\Attribute\Asset;

interface AssetInterface
{
    /**
     * @return string[]
     *
     * @internal
     */
    public function getLibraryPath(): array;
}
