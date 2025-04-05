<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\tests\fixtures\Lists\PintoListDependencies;

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
        ], PintoListDependencies::libraries());
    }
}
