<?php

declare(strict_types=1);

namespace Pinto\List;

use Pinto\Attribute\Asset\AssetInterface;
use Pinto\Attribute\Definition;
use Pinto\Attribute\DependencyOn;

/**
 * Implements interface defaults.
 *
 * @see ObjectListInterface
 *
 * @phpstan-require-implements \Pinto\List\ObjectListInterface
 */
trait ObjectListTrait
{
    public function getClass(): ?string
    {
        $rCase = new \ReflectionEnumUnitCase($this::class, $this->name);

        /** @var Definition|null $definitionAttr */
        $definitionAttr = ($rCase->getAttributes(Definition::class)[0] ?? null)?->newInstance();
        if (null === $definitionAttr) {
            return null;
        }

        return $definitionAttr->className;
    }

    public function name(): string
    {
        return match ($this) {
            default => $this instanceof \BackedEnum ? $this->value : $this->name,
        };
    }

    public function templateName(): string
    {
        return str_replace('_', '-', $this->name());
    }

    public function libraryName(): string
    {
        return match ($this) {
            default => $this instanceof \BackedEnum ? $this->value : $this->name,
        };
    }

    public function attachLibraries(): array
    {
        return [
            sprintf('pinto/%s', $this->libraryName()),
        ];
    }

    public function build(callable $wrapper, object $object): callable
    {
        return function (mixed $build) use ($wrapper) {
            // Override this trait fully, copy its contents, add your logic here.
            return $wrapper($build);
        };
    }

    public function assets(): iterable
    {
        $rComponentClass = null !== $this->getClass() ? new \ReflectionClass($this->getClass()) : null;

        /** @var array<\ReflectionAttribute<AssetInterface>> $assets */
        $assets = ($rComponentClass ?? new \ReflectionEnumUnitCase($this::class, $this->name))->getAttributes(AssetInterface::class, \ReflectionAttribute::IS_INSTANCEOF);

        // Else, try the enum if object or case does not have assets.
        if ([] === $assets) {
            $rEnum = new \ReflectionClass($this::class);
            $assets = $rEnum->getAttributes(AssetInterface::class, \ReflectionAttribute::IS_INSTANCEOF);
        }

        return array_map(fn (\ReflectionAttribute $r) => $r->newInstance(), $assets);
    }

    public function dependencies(): iterable
    {
        return \array_map(fn (\ReflectionClass|\ReflectionAttribute $r) => $r->newInstance(), [
            // This enum class.
            ...(new \ReflectionClass(static::class))->getAttributes(DependencyOn::class),
            // This case / instance.
            ...(new \ReflectionEnumUnitCase($this::class, $this->name))->getAttributes(DependencyOn::class),
        ]);
    }
}
