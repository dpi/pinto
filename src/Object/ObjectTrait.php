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
     * @param (callable (mixed $build): mixed) $wrapper
     *
     * @throws \Pinto\Exception\PintoMissingObjectMapping
     * @throws PintoBuildDefinitionMismatch
     */
    private function pintoBuild(callable $wrapper): mixed
    {
        static::$pintoEnum[static::class] ??= $this->pintoMapping()->getByClass(static::class);

        $template = [
            '#theme' => static::$pintoEnum[static::class]->name(),
            '#attached' => ['library' => static::$pintoEnum[static::class]->attachLibraries()],
        ];

        // A wrapper closure is used as to allow the enum to alter the build
        // for all enums (theme objects) under its control.
        $built = (static::$pintoEnum[static::class]->build($wrapper, $this))($template);

        if (is_array($built)) {
            // @todo assert keys in $built map those in themeDefinition()
            // allow extra keys ( things like # cache).
            // But dont allow missing keys.
            $missingKeys = array_diff($this->themeDefinitionKeysForComparison(), array_keys($built));
            if (count($missingKeys) > 0) {
                throw new PintoBuildDefinitionMismatch($this::class, $missingKeys);
            }
        }

        return $built;
    }

    /**
     * @return string[]
     *
     * @internal
     */
    private function themeDefinitionKeysForComparison(): array
    {
        $themeDefinition = $this->pintoMapping()->getThemeDefinition($this::class);

        return array_map(fn (string $varName): string => '#' . $varName, array_keys($themeDefinition['variables'] ?? []));
    }

    /**
     * Get a mapping object allowing for reverse reflection to object list.
     *
     * This can be done with a container/service or by constructing a PintoMapping
     * manually.
     */
    abstract private function pintoMapping(): PintoMapping;
}
