<?php

declare(strict_types=1);

namespace Pinto\Resource;

use Pinto\Attribute\DependencyOn;

interface ResourceInterface
{
    /**
     * Get the component class, or NULL if this resource does not have an associated class.
     *
     * @phpstan-return class-string<object>|null
     */
    public function getClass(): ?string;

    public function name(): string;

    /**
     * Get the template name.
     */
    public function templateName(): string;

    /**
     * Get the library name.
     */
    public function libraryName(): string;

    /**
     * Get the libraries to attach.
     *
     * @return string[]
     */
    public function attachLibraries(): array;

    /**
     * Build function called after an object build method.
     *
     * @param (callable (mixed $build): mixed) $wrapper
     */
    public function build(callable $wrapper, object $object): callable;

    /**
     * The template directory.
     */
    public function templateDirectory(): string;

    /**
     * Absolute CSS assets directory.
     */
    public function cssDirectory(): string;

    /**
     * Absolute JavaScript asset directory.
     */
    public function jsDirectory(): string;

    /**
     * Determine the [CSS & JS] assets for this object.
     *
     * @return iterable<\Pinto\Attribute\Asset\AssetInterface>
     */
    public function assets(): iterable;

    /**
     * @return iterable<DependencyOn>
     */
    public function dependencies(): iterable;
}
