<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\CanonicalProduct\Attribute\CanonicalProduct;
use Pinto\DefinitionDiscovery;
use Pinto\tests\fixtures\Lists\DependencyOn\PintoListDependencies;
use Pinto\tests\fixtures\Lists\DependencyOn\PintoListDependenciesHierarchyChild;
use Pinto\tests\fixtures\Lists\DependencyOn\PintoListDependenciesHierarchyParent;
use Pinto\tests\fixtures\Objects\DependencyOn\PintoObjectDependencyOnChild;
use Pinto\tests\fixtures\Objects\DependencyOn\PintoObjectDependencyOnParent;

use function Safe\realpath;

/**
 * Tests pinto enums where cases do not have definitions.
 *
 * @see PintoListDependencies
 */
final class PintoDependenciesTest extends TestCase
{
    /**
     * @covers \Pinto\List\ObjectListTrait::assets
     * @covers \Pinto\List\ObjectListTrait::libraries
     * @covers \Pinto\Attribute\DependencyOn
     */
    public function testNoAssets(): void
    {
        static::assertEquals([
            PintoListDependencies::Alpha->value => [
                'dependencies' => [
                    'pinto/beta',
                    'pinto/charlie',
                ],
            ],
            PintoListDependencies::Beta->value => [
                'js' => [
                    realpath(__DIR__ . '/fixtures/resources/javascript/app.js') => [
                        'minified' => false,
                        'preprocess' => false,
                        'attributes' => [],
                    ],
                ],
                'css' => [
                    'component' => [
                        realpath(__DIR__ . '/fixtures/resources/css/styles.css') => [
                            'minified' => false,
                            'preprocess' => false,
                            'category' => 'component',
                            'attributes' => [],
                        ],
                    ],
                ],
                'dependencies' => [
                    'pinto/charlie',
                ],
            ],
            PintoListDependencies::Charlie->value => [
                'js' => [
                    realpath(__DIR__ . '/fixtures/resources/javascript/app.js') => [
                        'minified' => false,
                        'preprocess' => false,
                        'attributes' => [],
                    ],
                ],
            ],
            PintoListDependencies::Delta->value => [
                'dependencies' => [
                    'pinto/alpha',
                    'foo/bar',
                ],
            ],
        ], PintoListDependencies::libraries(new \Pinto\PintoMapping([], [], [], [], [], [])));
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
            enumClasses: [],
            enums: [
                PintoObjectDependencyOnParent::class => [PintoListDependenciesHierarchyParent::class, PintoListDependenciesHierarchyParent::Parent->name],
            ],
            definitions: [],
            buildInvokers: [],
            types: [],
            lsbFactoryCanonicalObjectClasses: CanonicalProduct::discoverCanonicalProductObjectClasses($definitionDiscovery),
        );
        static::assertEquals([
            PintoListDependenciesHierarchyChild::Child->name => [
                'dependencies' => [
                    'pinto/Parent',
                ],
            ],
        ], PintoListDependenciesHierarchyChild::libraries($pintoMapping));
    }
}
