<?php

declare(strict_types=1);

namespace Pinto\Slots\Attribute;

/**
 * An attribute for renaming a slot at build time.
 *
 * An objects builder method must still reference original slot names in set().
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class RenameSlot
{
    public function __construct(
        public string|\UnitEnum $original,
        public string $new,
    ) {
    }
}
