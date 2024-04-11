<?php

declare(strict_types=1);

namespace Pinto\Attribute;

use Pinto\List\ObjectListInterface;

/**
 * An attribute for representing a library dependency to another enum.
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
final class DependencyOn
{
    /**
     * Constructs a dependency on attribute.
     *
     * @phpstan-param ObjectListInterface $case
     */
    public function __construct(
        public ObjectListInterface $case,
    ) {
    }
}
