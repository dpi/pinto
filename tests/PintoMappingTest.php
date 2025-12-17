<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Exception\PintoMissingObjectMapping;
use Pinto\List\Resource\ObjectListEnumResource;
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
        $pintoMapping = new PintoMapping(
            resources: [
                fixtures\Objects\PintoObject::class => ObjectListEnumResource::createFromEnum(fixtures\Lists\PintoList::Pinto_Object),
            ],
            definitions: [],
            buildInvokers: [],
            types: [],
            lsbFactoryCanonicalObjectClasses: [
                PintoObjectCanonicalProductRoot::class => PintoObjectCanonicalProductChild::class,
            ],
        );

        static::assertEquals(
            ObjectListEnumResource::createFromEnum(fixtures\Lists\PintoList::Pinto_Object),
            $pintoMapping->getResource(fixtures\Objects\PintoObject::class),
        );

        static::assertEquals(PintoObjectCanonicalProductChild::class, $pintoMapping->getCanonicalObjectClassName(PintoObjectCanonicalProductRoot::class));
        static::assertNull($pintoMapping->getCanonicalObjectClassName('other'));
    }

    public function testGetBuildInvokerException(): void
    {
        $pintoMapping = new PintoMapping([], [], [], [], []);
        static::expectException(PintoMissingObjectMapping::class);
        $pintoMapping->getBuildInvoker(fixtures\Objects\PintoObject::class);
    }

    public function testGetBuilderException(): void
    {
        $pintoMapping = new PintoMapping([], [], [], [], []);
        static::expectException(PintoMissingObjectMapping::class);
        $component = fixtures\Objects\PintoObject::create('Foo');
        $pintoMapping->getBuilder($component);
    }

    /**
     * @covers \Pinto\PintoMapping::getBuilder
     */
    public function testGetBuilder(): void
    {
        $pintoMapping = new PintoMapping(
            resources: [],
            definitions: [],
            buildInvokers: [
                fixtures\Objects\PintoObject::class => '__invoke',
            ],
            types: [],
            lsbFactoryCanonicalObjectClasses: [],
        );

        $component = fixtures\Objects\PintoObject::create('Foo');
        $builder = $pintoMapping->getBuilder($component);
        static::assertIsCallable($builder);

        $result = $builder();
        static::assertEquals('Foo', $result->pintoGet('test_variable'));
    }
}
