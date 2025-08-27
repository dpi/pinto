<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pinto\tests\fixtures\Lists\AssetGlob\PintoListAssetGlob;

/**
 * Test asset globs.
 *
 * @see PintoListAssetGlob
 */
final class PintoAssetGlobTest extends TestCase
{
    /**
     * Test globs.
     *
     * Each are relative to the asset directory.
     * There is no implied relevance to the PHP object location, nor is there a
     * requirement for a #[Definition].
     *
     * @covers \Pinto\Attribute\Asset\Css::getLibraryPaths
     * @covers \Pinto\Attribute\Asset\Js::getLibraryPaths
     */
    public function testGlob(): void
    {
        static::assertEquals([
            PintoListAssetGlob::Wildcard->name => [
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
        ], PintoListAssetGlob::libraries(new Pinto\PintoMapping([], [], [], [], [], [])));
    }
}
