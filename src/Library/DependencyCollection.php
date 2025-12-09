<?php

declare(strict_types=1);

namespace Pinto\Library;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<mixed>
 */
final class DependencyCollection extends AbstractCollection
{
    /**
     * @param array<mixed> $dependencies
     */
    private function __construct(array $dependencies)
    {
        parent::__construct($dependencies);
    }

    /**
     * @param array<mixed> $dependencies
     */
    public static function create(array $dependencies): static
    {
        return new static($dependencies);
    }

    public function getType(): string
    {
        return 'mixed';
    }
}
