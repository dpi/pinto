<?php

declare(strict_types=1);

namespace Pinto\Attribute;

use Pinto\List\ObjectListInterface;

/**
 * An attribute for representing a library dependency to another enum or a manual dependency.
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
final class DependencyOn
{
    /**
     * Constructs a dependency.
     *
     * @phpstan-param ObjectListInterface|string $dependency
     */
    public function __construct(
        public ObjectListInterface|string $dependency,
    ) {
    }
}
