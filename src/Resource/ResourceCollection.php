<?php

declare(strict_types=1);

namespace Pinto\Resource;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<ResourceInterface>
 */
final class ResourceCollection extends AbstractCollection implements ResourceCollectionInterface
{
    private bool $immutable = false;

    /**
     * @param array<string, ResourceInterface> $resources
     */
    private function __construct(array $resources)
    {
        parent::__construct($resources);
    }

    /**
     * @param array<string, ResourceInterface> $resources
     */
    public static function create(array $resources): static
    {
        $c = new static($resources);
        $c->immutable = true;

        return $c;
    }

    public function getType(): string
    {
        return '\\Pinto\\Resource\\ResourceInterface';
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($this->immutable) {
            throw new \LogicException('Collection is immutable');
        }

        parent::offsetSet($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \LogicException('Collection is immutable');
    }
}
