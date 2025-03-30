<?php

declare(strict_types=1);

namespace Pinto\Asset;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends \Ramsey\Collection\AbstractCollection<string[]>
 */
final class AssetLibraryPaths extends AbstractCollection
{
    public function getType(): string
    {
        return 'array';
    }
}
