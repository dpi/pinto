<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pinto\tests\fixtures\Lists\AssetGlob\PintoListAssetEnum;

/**
 * Test asset attribute at enum level.
 */
final class PintoAssetEnumTest extends TestCase
{
    /**
     * Test asset attribute above enum.
     *
     * @covers \Pinto\List\ObjectListTrait::assets
     */
    public function testEnumAssetAttribute(): void
    {
        static::assertEquals([
            PintoListAssetEnum::Obj->name => [
                'css' => [
                    'component' => [
                        'tests/fixtures/Assets/PintoListAssetGlob/styles1.css' => [
                            'minified' => false,
                            'preprocess' => false,
                            'category' => 'component',
                            'attributes' => [],
                        ],
                        'tests/fixtures/Assets/PintoListAssetGlob/styles2.css' => [
                            'minified' => false,
                            'preprocess' => false,
                            'category' => 'component',
                            'attributes' => [],
                        ],
                    ],
                ],
                'js' => [
                    'tests/fixtures/Assets/PintoListAssetGlob/script1.js' => [
                        'minified' => false,
                        'preprocess' => false,
                        'attributes' => [],
                    ],
                    'tests/fixtures/Assets/PintoListAssetGlob/script2.js' => [
                        'minified' => false,
                        'preprocess' => false,
                        'attributes' => [],
                    ],
                ],
            ],
        ], PintoListAssetEnum::libraries());
    }
}
