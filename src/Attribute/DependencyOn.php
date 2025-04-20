<?php

declare(strict_types=1);

namespace Pinto\Attribute;

use Pinto\List\ObjectListInterface;

/**
 * An attribute for representing a library dependency to another enum or a manual dependency.
 *
 * When applied to an enum class, all enum cases will receive the same dependency.
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
final class DependencyOn
{
    private const useNamedParameters = 'Using this attribute without named parameters is not supported.';

    /**
     * Constructs a dependency.
     *
     * @phpstan-param ObjectListInterface|string $dependency
     */
    public function __construct(
        public ObjectListInterface|string|null $dependency = null,
        string $useNamedParameters = self::useNamedParameters,
        public bool $parent = false,
    ) {
        if (self::useNamedParameters !== $useNamedParameters) {
            throw new \LogicException(self::useNamedParameters);
        }

        if (null === $dependency && false === $parent) {
            throw new \LogicException(sprintf('%s is not configured.', DependencyOn::class));
        }

        if (null !== $dependency && false !== $parent) {
            throw new \LogicException(sprintf('%s must not have both $dependency and $parent configured. Repeat the attribute to use both.', DependencyOn::class));
        }
    }
}
