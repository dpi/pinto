<?php

declare(strict_types=1);

namespace Pinto\tests\Attribute;

use PHPUnit\Framework\TestCase;
use Pinto\Attribute;
use Pinto\Attribute\Build;
use Pinto\Exception\PintoBuild;
use Pinto\tests\fixtures\Lists\PintoList;
use Pinto\tests\fixtures\Objects\Build\Faulty\PintoMissingBuildMethodObject;
use Pinto\tests\fixtures\Objects\Build\PintoBuildMethodObject;

/**
 * @covers \Pinto\Attribute\Build
 * @covers \Pinto\Attribute\DependencyOn
 */
final class AttributesTest extends TestCase
{
    public function testDependencyOn(): void
    {
        $dependencyOn = new Attribute\DependencyOn(PintoList::Pinto_Object);
        self::assertEquals(PintoList::Pinto_Object, $dependencyOn->dependency);
    }

    /**
     * @covers \Pinto\Attribute\Build::buildMethodForThemeObject
     */
    public function testBuildMethodForThemeObject(): void
    {
        static::assertEquals('__invoke', Build::buildMethodForThemeObject(\Pinto\tests\fixtures\Objects\PintoObject::class));
        static::assertEquals('builder', Build::buildMethodForThemeObject(PintoBuildMethodObject::class));
        static::expectException(PintoBuild::class);
        static::expectExceptionMessage(sprintf('%s attribute or __invoke() method on %s', Build::class, PintoMissingBuildMethodObject::class));
        static::assertEquals('builder', Build::buildMethodForThemeObject(PintoMissingBuildMethodObject::class));
    }
}
