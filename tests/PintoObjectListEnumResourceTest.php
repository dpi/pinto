<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\List\Resource\ObjectListEnumResource;
use Pinto\tests\fixtures\Lists\PintoList;

/**
 * @coversDefaultClass \Pinto\List\Resource\ObjectListEnumResource
 */
final class PintoObjectListEnumResourceTest extends TestCase
{
    public function testImmutabilityOffsetUnet(): void
    {
        $enum = PintoList::Pinto_Object;
        $resource = ObjectListEnumResource::createFromEnum($enum);
        static::assertEquals($enum->getClass(), $resource->getClass());
        static::assertEquals($enum->name(), $resource->name());
        static::assertEquals($enum->templateName(), $resource->templateName());
        static::assertEquals($enum->libraryName(), $resource->libraryName());
        static::assertEquals($enum->attachLibraries(), $resource->attachLibraries());
        static::assertEquals($enum->templateDirectory(), $resource->templateDirectory());
        static::assertEquals($enum->cssDirectory(), $resource->cssDirectory());
        static::assertEquals($enum->jsDirectory(), $resource->jsDirectory());
        static::assertEquals($enum->assets(), $resource->assets());
        static::assertEquals($enum->dependencies(), $resource->dependencies());
    }
}
