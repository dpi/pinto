<?php

declare(strict_types=1);

namespace Pinto\Attribute;

/**
 * An attribute for representing which class an enum represents.
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS_CONSTANT)]
final class Definition
{
    /**
     * Constructs a definition attribute.
     *
     * @phpstan-param class-string $className
     */
    public function __construct(
        public string $className,
    ) {
    }
}
