<?php

declare(strict_types=1);

namespace Pinto\List\Resource;

use Pinto\List\ObjectListInterface;
use Pinto\Resource\ResourceInterface;

/**
 * An adapter to an enum.
 *
 * @internal
 */
final class ObjectListEnumResource implements ResourceInterface
{
    private function __construct(
        public readonly ObjectListInterface $pintoEnum,
    ) {
    }

    public static function createFromEnum(ObjectListInterface $pintoEnum): static
    {
        return new static($pintoEnum);
    }

    public function getClass(): ?string
    {
        return $this->pintoEnum->getClass();
    }

    public function name(): string
    {
        return $this->pintoEnum->name();
    }

    public function templateName(): string
    {
        return $this->pintoEnum->templateName();
    }

    public function libraryName(): string
    {
        return $this->pintoEnum->libraryName();
    }

    public function attachLibraries(): array
    {
        return $this->pintoEnum->attachLibraries();
    }

    public function build(callable $wrapper, object $object): callable
    {
        return $this->pintoEnum->build($wrapper, $object);
    }

    public function templateDirectory(): string
    {
        return $this->pintoEnum->templateDirectory();
    }

    public function cssDirectory(): string
    {
        return $this->pintoEnum->cssDirectory();
    }

    public function jsDirectory(): string
    {
        return $this->pintoEnum->jsDirectory();
    }

    public function assets(): iterable
    {
        return $this->pintoEnum->assets();
    }

    public function dependencies(): iterable
    {
        return $this->pintoEnum->dependencies();
    }
}
