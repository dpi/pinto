<?php

declare(strict_types=1);

namespace Pinto\List;

/**
 * An implementation where all resources are in a single directory.
 *
 * @see \Pinto\Resource\ResourceInterface
 *
 * @phpstan-require-implements \Pinto\Resource\ResourceInterface
 */
trait SingleDirectoryObjectListTrait
{
    public function templateDirectory(): string
    {
        return $this->directory();
    }

    public function cssDirectory(): string
    {
        return $this->directory();
    }

    public function jsDirectory(): string
    {
        return $this->directory();
    }

    abstract public function directory(): string;
}
