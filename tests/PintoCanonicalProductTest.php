<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pinto\CanonicalProduct\Attribute\CanonicalProduct;
use Pinto\CanonicalProduct\Exception\PintoMultipleCanonicalProduct;
use Pinto\DefinitionDiscovery;
use Pinto\Slots\Build;
use Pinto\tests\fixtures\Lists\CanonicalProduct\PintoListCanonicalProductContested;
use Pinto\tests\fixtures\Lists\CanonicalProduct\PintoListCanonicalProductHierarchy;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductChild;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductContestedChild1;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductContestedChild2;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductContestedRoot;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductHierarchyChild;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductHierarchyChild2;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductHierarchyGrandParent;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductHierarchyGreatGrandParent;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductHierarchyParent;
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
        $object = PintoObjectCanonicalProductWithoutAttrChild::factoryCreate('foo bar');
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
        PintoObjectCanonicalProductContestedRoot::factoryCreate();
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

    /**
     * @covers \Pinto\CanonicalProduct\Attribute\CanonicalProduct::discoverCanonicalProductObjectClasses
     */
    public function testDiscoverCanonicalProductHierarchy(): void
    {
        $definitionDiscovery = new DefinitionDiscovery();
        $definitionDiscovery[PintoObjectCanonicalProductHierarchyGrandParent::class] = PintoListCanonicalProductHierarchy::GrandParent;
        $definitionDiscovery[PintoObjectCanonicalProductHierarchyParent::class] = PintoListCanonicalProductHierarchy::Parent;
        $definitionDiscovery[PintoObjectCanonicalProductHierarchyChild::class] = PintoListCanonicalProductHierarchy::Child;
        self::assertEquals([
            // Parent is eliminated.
            PintoObjectCanonicalProductHierarchyGrandParent::class => PintoObjectCanonicalProductHierarchyChild::class,
        ], CanonicalProduct::discoverCanonicalProductObjectClasses($definitionDiscovery));
    }

    /**
     * Where two children extend a parent extend a root.
     *
     * The parent is correctly eliminated, but children don't extend each-other.
     *
     * Where:
     * R1
     * └── P1
     *     ├── C1
     *     └── C2
     *
     * @covers \Pinto\CanonicalProduct\Attribute\CanonicalProduct::discoverCanonicalProductObjectClasses
     */
    public function testDiscoverCanonicalProductHierarchyContested(): void
    {
        $definitionDiscovery = new DefinitionDiscovery();
        $definitionDiscovery[PintoObjectCanonicalProductHierarchyGrandParent::class] = PintoListCanonicalProductHierarchy::GrandParent;
        $definitionDiscovery[PintoObjectCanonicalProductHierarchyParent::class] = PintoListCanonicalProductHierarchy::Parent;
        $definitionDiscovery[PintoObjectCanonicalProductHierarchyChild::class] = PintoListCanonicalProductHierarchy::Child;
        $definitionDiscovery[PintoObjectCanonicalProductHierarchyChild2::class] = PintoListCanonicalProductHierarchy::Child2;

        static::expectException(PintoMultipleCanonicalProduct::class);
        static::expectExceptionMessage('Multiple objects are contested to override object `' . PintoObjectCanonicalProductHierarchyGrandParent::class . '` where only one is permitted: ' . PintoObjectCanonicalProductHierarchyChild::class . ', ' . PintoObjectCanonicalProductHierarchyChild2::class);
        CanonicalProduct::discoverCanonicalProductObjectClasses($definitionDiscovery);
    }

    /**
     * Ensure object hierarchy stops on the first object without #[CanonicalProduct].
     *
     * Ensure hierarchy stops at PintoObjectCanonicalProductHierarchyGrandParent instead of going up the chain to PintoObjectCanonicalProductHierarchyGreatGrandParent.
     *
     * @covers \Pinto\CanonicalProduct\Attribute\CanonicalProduct::discoverCanonicalProductObjectClasses
     */
    public function testDiscoverCanonicalProductHierarchyStopsAtFirstNonCanonical(): void
    {
        $definitionDiscovery = new DefinitionDiscovery();
        $definitionDiscovery[PintoObjectCanonicalProductHierarchyGreatGrandParent::class] = PintoListCanonicalProductHierarchy::GreatGrandParent;
        $definitionDiscovery[PintoObjectCanonicalProductHierarchyGrandParent::class] = PintoListCanonicalProductHierarchy::GrandParent;
        $definitionDiscovery[PintoObjectCanonicalProductHierarchyParent::class] = PintoListCanonicalProductHierarchy::Parent;
        $definitionDiscovery[PintoObjectCanonicalProductHierarchyChild::class] = PintoListCanonicalProductHierarchy::Child;
        self::assertEquals([
            // See GreatGrandParent isn't resolved as root since the `hasAttribute` check is in place.
            PintoObjectCanonicalProductHierarchyGrandParent::class => PintoObjectCanonicalProductHierarchyChild::class,
        ], CanonicalProduct::discoverCanonicalProductObjectClasses($definitionDiscovery));
    }

    public function testCanonicalProductAttributeOnList(): void
    {
        $object = PintoObjectCanonicalProductOnListRoot1::factoryCreate();
        static::assertInstanceOf(PintoObjectCanonicalProductOnListChild1::class, $object);

        $object = PintoObjectCanonicalProductOnListRoot2::factoryCreate();
        static::assertInstanceOf(PintoObjectCanonicalProductOnListChild2::class, $object);
    }
}
