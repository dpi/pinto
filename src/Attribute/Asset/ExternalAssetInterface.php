<?php

declare(strict_types=1);

namespace Pinto\Attribute\Asset;

interface ExternalAssetInterface extends AssetInterface
{
    public function getUrl(): string;
}
