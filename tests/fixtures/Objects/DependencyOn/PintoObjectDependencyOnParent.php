<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\DependencyOn;

use Pinto\Attribute\ObjectType;
use Pinto\CanonicalProduct\CanonicalFactoryTrait;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;

/**
 * DependencyOn test object.
 */
#[ObjectType\Slots]
class PintoObjectDependencyOnParent
{
    use ObjectTrait;

    use CanonicalFactoryTrait;

    final public function __construct()
    {
    }

    public function __invoke(): mixed
    {
        throw new \LogicException('Object level logic not tested.');
    }

    private static function pintoMappingStatic(): PintoMapping
    {
        throw new \LogicException('Not tested.');
    }

    private function pintoMapping(): PintoMapping
    {
        return self::pintoMappingStatic();
    }
}
