<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\Js;
use Pinto\Library\LibraryBuilder;
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
            new Css(path: 'styles*.css'),
            new Js(path: 'script*.js'),
        ], PintoListAssetGlob::Wildcard->assets());

        static::assertEquals([
            [(new Css('styles*.css'))->setPath('tests/fixtures/Assets/PintoListAssetGlob'), [
                'css', 'component', 'tests/fixtures/Assets/PintoListAssetGlob/styles1.css',
            ]],
            [(new Css('styles*.css'))->setPath('tests/fixtures/Assets/PintoListAssetGlob'), [
                'css', 'component', 'tests/fixtures/Assets/PintoListAssetGlob/styles2.css',
            ]],
            [(new Js('script*.js'))->setPath('tests/fixtures/Assets/PintoListAssetGlob'), [
                'js', 'tests/fixtures/Assets/PintoListAssetGlob/script1.js',
            ]],
            [(new Js('script*.js'))->setPath('tests/fixtures/Assets/PintoListAssetGlob'), [
                'js', 'tests/fixtures/Assets/PintoListAssetGlob/script2.js',
            ]],
        ], iterator_to_array(LibraryBuilder::expandLibraryPaths(PintoListAssetGlob::Wildcard)));
    }
}
