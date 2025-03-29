<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pinto\CanonicalProduct\Attribute\CanonicalProduct;
use Pinto\CanonicalProduct\Exception\PintoMultipleCanonicalProduct;
use Pinto\DefinitionDiscovery;
use Pinto\Slots\Build;
use Pinto\tests\fixtures\Lists\CanonicalProduct\PintoListCanonicalProductContested;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductChild;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductContestedChild1;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductContestedChild2;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductContestedRoot;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductOnListChild1;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductOnListChild2;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductOnListRoot1;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductOnListRoot2;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductRoot;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductWithoutAttrChild;

final class PintoCanonicalProductTest extends TestCase
{
    public function testCreate(): void
    {
        $text = 'foo bar';
        $object = PintoObjectCanonicalProductRoot::customCreate($text);
        $build = $object();
        static::assertInstanceOf(Build::class, $build);
        static::assertEquals($text . ' built!', $build->pintoGet('text'));
    }

    /**
     * Object inherits use of CanonicalFactoryTrait, but does not have #[CanonicalProduct] on the object or related enum list.
     */
    public function testCreateUsesTraitWithoutAttribute(): void
    {
        $object = PintoObjectCanonicalProductWithoutAttrChild::create('foo bar');
        static::assertInstanceOf(PintoObjectCanonicalProductWithoutAttrChild::class, $object);
    }

    public function testCreateIncorrectEntryPoint(): void
    {
        static::expectException(BadMethodCallException::class);
        static::expectExceptionMessage(sprintf('Object factory should be called with `%s::customCreate` instead of `%s::customCreate`', PintoObjectCanonicalProductRoot::class, PintoObjectCanonicalProductChild::class));
        PintoObjectCanonicalProductChild::customCreate('foo');
    }

    public function testContestedCanonicalProduct(): void
    {
        static::expectException(PintoMultipleCanonicalProduct::class);
        static::expectExceptionMessage('Multiple objects are contested to override object `' . PintoObjectCanonicalProductContestedRoot::class . '` where only one is permitted: ' . PintoObjectCanonicalProductContestedChild1::class . ', ' . PintoObjectCanonicalProductContestedChild2::class);
        PintoObjectCanonicalProductContestedRoot::create();
    }

    /**
     * @covers \Pinto\CanonicalProduct\Attribute\CanonicalProduct::discoverCanonicalProductObjectClasses
     */
    public function testDiscoverCanonicalProductObjectClasses(): void
    {
        static::expectException(PintoMultipleCanonicalProduct::class);
        static::expectExceptionMessage('Multiple objects are contested to override object `' . PintoObjectCanonicalProductContestedRoot::class . '` where only one is permitted: ' . PintoObjectCanonicalProductContestedChild1::class . ', ' . PintoObjectCanonicalProductContestedChild2::class);
        $definitionDiscovery = new DefinitionDiscovery();
        $definitionDiscovery[PintoObjectCanonicalProductContestedChild1::class] = PintoListCanonicalProductContested::Child1;
        $definitionDiscovery[PintoObjectCanonicalProductContestedChild2::class] = PintoListCanonicalProductContested::Child2;
        $definitionDiscovery[PintoObjectCanonicalProductContestedRoot::class] = PintoListCanonicalProductContested::Root;
        CanonicalProduct::discoverCanonicalProductObjectClasses($definitionDiscovery);
    }

    public function testCanonicalProductAttributeOnList(): void
    {
        $object = PintoObjectCanonicalProductOnListRoot1::create();
        static::assertInstanceOf(PintoObjectCanonicalProductOnListChild1::class, $object);

        $object = PintoObjectCanonicalProductOnListRoot2::create();
        static::assertInstanceOf(PintoObjectCanonicalProductOnListChild2::class, $object);
    }
}
