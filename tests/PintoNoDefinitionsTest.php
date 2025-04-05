<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\tests\fixtures\Lists\PintoListNoDefinitions;

use function Safe\realpath;

/**
 * Tests pinto enums where cases do not have definitions.
 *
 * @see PintoListNoDefinitions
 */
final class PintoNoDefinitionsTest extends TestCase
{
    /**
     * @covers \Pinto\List\ObjectListTrait::definitions
     */
    public function testNoThemeDefinitions(): void
    {
        static::assertCount(0, PintoListNoDefinitions::definitions(new \Pinto\DefinitionDiscovery()));
    }

    /**
     * @covers \Pinto\List\ObjectListTrait::assets
     * @covers \Pinto\List\ObjectListTrait::libraries
     */
    public function testNoAssets(): void
    {
        static::assertEquals([
            PintoListNoDefinitions::No_Definition_With_Assets->value => [
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
            ],
        ], PintoListNoDefinitions::libraries());
    }
}
