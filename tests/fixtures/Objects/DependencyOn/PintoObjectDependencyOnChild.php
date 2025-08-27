<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\DependencyOn;

use Pinto\CanonicalProduct\Attribute\CanonicalProduct;

/**
 * DependencyOn test object.
 */
#[CanonicalProduct]
final class PintoObjectDependencyOnChild extends PintoObjectDependencyOnParent
{
}
