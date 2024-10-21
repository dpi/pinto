<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\tests\fixtures\Lists\PintoListNoDefinitions;

/**
 * Tests pinto enums where cases do not have definitions.
 *
 * @see PintoListNoDefinitions
 */
final class PintoNoDefinitionsTest extends TestCase
{
    /**
     * @covers \Pinto\List\ObjectListTrait::themeDefinitions
     */
    public function testNoThemeDefinitions(): void
    {
        static::assertCount(0, PintoListNoDefinitions::definitions());
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
            ],
        ], PintoListNoDefinitions::libraries());
    }
}
