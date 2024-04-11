<?php

declare(strict_types=1);

namespace Pinto\Attribute\Asset;

interface LocalAssetInterface extends AssetInterface
{
    /**
     * @return $this
     */
    public function setPath(string $assetPath);
}
