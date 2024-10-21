<?php

declare(strict_types=1);

namespace Pinto\Object;

use Pinto\Exception\PintoBuildDefinitionMismatch;
use Pinto\PintoMapping;

/**
 * An optional trait for theme objects.
 */
trait ObjectTrait
{
    /**
     * Memo/cache.
     *
     * @var array<class-string<object>, \Pinto\List\ObjectListInterface>
     *
     * @internal
     */
    protected static array $pintoEnum = [];

    /**
     * An optional wrapper for individual object build methods.
     *
     * Calling this applies:
     * - Extra safety checks
     * - Applies '#theme' and attaches libraries.
     * - Calls a wrapper build method on the main object list enum.
     *
     * @param (callable (W $build): W) $wrapper
     *
     * @throws \Pinto\Exception\PintoMissingObjectMapping
     * @throws PintoBuildDefinitionMismatch
     *
     * @template W
     */
    private function pintoBuild(callable $wrapper): mixed
    {
        static::$pintoEnum[static::class] ??= $this->pintoMapping()->getByClass(static::class);

        $objectType = $this->pintoMapping()->getObjectType(static::class);

        $template = $objectType::createBuild(static::$pintoEnum[static::class], static::class);

        // A wrapper closure is used as to allow the enum to alter the build
        // for all enums (theme objects) under its control.
        $built = (static::$pintoEnum[static::class]->build($wrapper, $this))($template);

        $definition = $this->pintoMapping()->getThemeDefinition($this::class);
        $objectType::validateBuild($built, $definition, static::class);

        return $built;
    }

    /**
     * Get a mapping object allowing for reverse reflection to object list.
     *
     * This can be done with a container/service or by constructing a PintoMapping
     * manually.
     */
    abstract private function pintoMapping(): PintoMapping;
}
