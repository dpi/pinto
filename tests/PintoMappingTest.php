<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Exception\PintoMissingObjectMapping;
use Pinto\PintoMapping;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductChild;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductRoot;

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
        ], [], [], [], [
            PintoObjectCanonicalProductRoot::class => PintoObjectCanonicalProductChild::class,
        ]);

        static::assertEquals([
            fixtures\Lists\PintoList::class,
        ], $pintoMapping->getEnumClasses());

        static::assertEquals(fixtures\Lists\PintoList::Pinto_Object, $pintoMapping->getByClass(fixtures\Objects\PintoObject::class));

        static::assertEquals(PintoObjectCanonicalProductChild::class, $pintoMapping->getCanonicalObjectClassName(PintoObjectCanonicalProductRoot::class));
        static::assertNull($pintoMapping->getCanonicalObjectClassName('other'));
    }

    public function testGetBuildInvokerException(): void
    {
        $pintoMapping = new PintoMapping([], [], [], [], [], []);
        static::expectException(PintoMissingObjectMapping::class);
        $pintoMapping->getBuildInvoker(fixtures\Objects\PintoObject::class);
    }
}
