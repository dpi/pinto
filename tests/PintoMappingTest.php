<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Exception\PintoMissingObjectMapping;
use Pinto\PintoMapping;

/**
 * @coversDefaultClass \Pinto\PintoMapping
 */
final class PintoMappingTest extends TestCase
{
    public function testPintoMapping(): void
    {
        $pintoMapping = new PintoMapping([
            fixtures\Lists\PintoList::class,
        ], [
            fixtures\Objects\PintoObject::class => [
                fixtures\Lists\PintoList::class, fixtures\Lists\PintoList::Pinto_Object->name,
            ],
        ], [], [], [], []);

        static::assertEquals([
            fixtures\Lists\PintoList::class,
        ], $pintoMapping->getEnumClasses());

        static::assertEquals(fixtures\Lists\PintoList::Pinto_Object, $pintoMapping->getByClass(fixtures\Objects\PintoObject::class));
    }

    public function testGetBuildInvokerException(): void
    {
        $pintoMapping = new PintoMapping([], [], [], [], [], []);
        static::expectException(PintoMissingObjectMapping::class);
        $pintoMapping->getBuildInvoker(fixtures\Objects\PintoObject::class);
    }
}
