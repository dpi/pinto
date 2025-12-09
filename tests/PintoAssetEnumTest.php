<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\Js;
use Pinto\Library\LibraryBuilder;
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
            new Css(),
            new Js(),
        ], PintoListAssetEnum::Obj->assets());

        static::assertEquals([
            [(new Css())->setPath('tests/fixtures/Assets/PintoListAssetGlob'), [
                'css', 'component', 'tests/fixtures/Assets/PintoListAssetGlob/styles1.css',
            ]],
            [(new Css())->setPath('tests/fixtures/Assets/PintoListAssetGlob'), [
                'css', 'component', 'tests/fixtures/Assets/PintoListAssetGlob/styles2.css',
            ]],
            [(new Js())->setPath('tests/fixtures/Assets/PintoListAssetGlob'), [
                'js', 'tests/fixtures/Assets/PintoListAssetGlob/script1.js',
            ]],
            [(new Js())->setPath('tests/fixtures/Assets/PintoListAssetGlob'), [
                'js', 'tests/fixtures/Assets/PintoListAssetGlob/script2.js',
            ]],
        ], iterator_to_array(LibraryBuilder::expandLibraryPaths(PintoListAssetEnum::Obj)));
    }
}
