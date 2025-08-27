<?php

declare(strict_types=1);

namespace Pinto\List;

use Pinto\DefinitionCollection;
use Pinto\DefinitionDiscovery;
use Pinto\PintoMapping;

/**
 * an interface.
 */
interface ObjectListInterface extends \UnitEnum
{
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
     * Determine the assets for this object.
     *
     * @return iterable<\Pinto\Attribute\Asset\AssetInterface>
     */
    public function assets(): iterable;

    /**
     * Get all theme definitions for all components.
     *
     * @internal
     */
    public static function definitions(DefinitionDiscovery $definitionDiscovery): DefinitionCollection;

    /**
     * Get all library definitions for all components.
     *
     * @phpstan-return array<string, array{css?: array<string, array<string, array<mixed>>>, js?: array<string, array<mixed>>}>
     *
     * @internal
     */
    public static function libraries(PintoMapping $pintoMapping): array;
}
