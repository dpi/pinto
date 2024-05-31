<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\tests\fixtures\Lists\PintoListDependencies;

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
                    'tests/fixtures/resources/app.js' => [
                        'minified' => false,
                        'preprocess' => false,
                        'attributes' => [],
                    ],
                ],
                'css' => [
                    'component' => [
                        'tests/fixtures/resources/styles.css' => [
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
                    'tests/fixtures/resources/app.js' => [
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
        ], PintoListDependencies::libraries());
    }
}
