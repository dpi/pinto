<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Attribute\DependencyOn;
use Pinto\CanonicalProduct\Attribute\CanonicalProduct;
use Pinto\DefinitionDiscovery;
use Pinto\Library\DependencyCollection;
use Pinto\Library\LibraryBuilder;
use Pinto\List\Resource\ObjectListEnumResource;
use Pinto\tests\fixtures\Lists\DependencyOn\PintoListDependencies;
use Pinto\tests\fixtures\Lists\DependencyOn\PintoListDependenciesHierarchyChild;
use Pinto\tests\fixtures\Lists\DependencyOn\PintoListDependenciesHierarchyParent;
use Pinto\tests\fixtures\Objects\DependencyOn\PintoObjectDependencyOnChild;
use Pinto\tests\fixtures\Objects\DependencyOn\PintoObjectDependencyOnParent;

/**
 * Tests pinto enums where cases do not have definitions.
 *
 * @see PintoListDependencies
 */
final class PintoDependenciesTest extends TestCase
{
    /**
     * @covers \Pinto\List\ObjectListTrait::assets
     * @covers \Pinto\Attribute\DependencyOn
     * @covers \Pinto\Library\LibraryBuilder::solveDeps
     */
    public function testAssets(): void
    {
        $pintoMapping = new \Pinto\PintoMapping(
            resources: [
                '--test-1' => ObjectListEnumResource::createFromEnum(PintoListDependencies::Alpha),
                '--test-2' => ObjectListEnumResource::createFromEnum(PintoListDependencies::Beta),
                '--test-3' => ObjectListEnumResource::createFromEnum(PintoListDependencies::Charlie),
                '--test-4' => ObjectListEnumResource::createFromEnum(PintoListDependencies::Delta),
            ],
            definitions: [],
            buildInvokers: [],
            types: [],
            lsbFactoryCanonicalObjectClasses: [],
        );

        static::assertEquals([
            new DependencyOn(PintoListDependencies::Beta),
            new DependencyOn(PintoListDependencies::Charlie),
        ], PintoListDependencies::Alpha->dependencies());
        static::assertEquals([
            new DependencyOn(PintoListDependencies::Charlie),
        ], PintoListDependencies::Beta->dependencies());
        static::assertEquals([], PintoListDependencies::Charlie->dependencies());
        static::assertEquals([
            new DependencyOn(PintoListDependencies::Alpha),
            new DependencyOn(dependency: 'foo/bar'),
        ], PintoListDependencies::Delta->dependencies());

        static::assertEquals(DependencyCollection::create([
            ObjectListEnumResource::createFromEnum(PintoListDependencies::Beta),
            ObjectListEnumResource::createFromEnum(PintoListDependencies::Charlie),
        ]), LibraryBuilder::solveDeps(PintoListDependencies::Alpha, $pintoMapping));

        static::assertEquals(DependencyCollection::create([
            ObjectListEnumResource::createFromEnum(PintoListDependencies::Charlie),
        ]), LibraryBuilder::solveDeps(PintoListDependencies::Beta, $pintoMapping));

        static::assertEquals(DependencyCollection::create([]), LibraryBuilder::solveDeps(PintoListDependencies::Charlie, $pintoMapping));

        static::assertEquals(DependencyCollection::create([
            ObjectListEnumResource::createFromEnum(PintoListDependencies::Alpha),
            'foo/bar',
        ]), LibraryBuilder::solveDeps(PintoListDependencies::Delta, $pintoMapping));
    }

    /**
     * Test DependencyOn(parent).
     */
    public function testDependencyOnParent(): void
    {
        $definitionDiscovery = new DefinitionDiscovery();
        $definitionDiscovery[PintoObjectDependencyOnChild::class] = PintoListDependenciesHierarchyChild::Child;
        $definitionDiscovery[PintoObjectDependencyOnParent::class] = PintoListDependenciesHierarchyParent::Parent;

        $pintoMapping = new \Pinto\PintoMapping(
            resources: [
                PintoObjectDependencyOnChild::class => ObjectListEnumResource::createFromEnum(PintoListDependenciesHierarchyChild::Child),
                PintoObjectDependencyOnParent::class => ObjectListEnumResource::createFromEnum(PintoListDependenciesHierarchyParent::Parent),
            ],
            definitions: [],
            buildInvokers: [],
            types: [],
            lsbFactoryCanonicalObjectClasses: CanonicalProduct::discoverCanonicalProductObjectClasses($definitionDiscovery),
        );

        static::assertEquals([
            new DependencyOn(parent: true),
        ], PintoListDependenciesHierarchyChild::Child->dependencies());

        static::assertEquals(DependencyCollection::create([
            ObjectListEnumResource::createFromEnum(PintoListDependenciesHierarchyParent::Parent),
        ]), LibraryBuilder::solveDeps(PintoListDependenciesHierarchyChild::Child, $pintoMapping));
    }
}
