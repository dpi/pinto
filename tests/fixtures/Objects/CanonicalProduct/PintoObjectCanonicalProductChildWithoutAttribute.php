<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\CanonicalProduct;

use Pinto\CanonicalProduct\Attribute\CanonicalProduct;

/**
 * A child class that uses CanonicalFactory trait, but does not have or inherit #[CanonicalProduct].
 */
final class PintoObjectCanonicalProductChildWithoutAttribute extends PintoObjectCanonicalProductRoot
{
}
