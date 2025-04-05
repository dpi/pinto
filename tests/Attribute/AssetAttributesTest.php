<?php

declare(strict_types=1);

namespace Pinto\tests\Attribute;

use PHPUnit\Framework\TestCase;
use Pinto\Asset\AssetLibraryPaths;
use Pinto\Attribute\Asset;

use function Safe\realpath;

/**
 * @covers \Pinto\Attribute\Asset\Css
 * @covers \Pinto\Attribute\Asset\ExternalCss
 * @covers \Pinto\Attribute\Asset\ExternalJs
 * @covers \Pinto\Attribute\Asset\Js
 */
final class AssetAttributesTest extends TestCase
{
    public function testJs(): void
    {
        $js = new Asset\Js('foo.js');
        $js->setPath(realpath(__DIR__ . '/../fixtures/resources/javascript'));
        static::assertEquals(new AssetLibraryPaths([
            ['js', realpath(__DIR__ . '/../fixtures/resources/javascript/foo.js')],
        ]), $js->getLibraryPaths());
    }

    /**
     * @covers \Pinto\Attribute\Asset\Js::getLibraryPaths
     */
    public function testJsFileMissing(): void
    {
        $js = new Asset\Js('does-not-exist.js');
        $js->setPath(realpath(__DIR__ . '/../fixtures/resources/javascript'));
        static::expectExceptionMessage('File does not exist');
        $js->getLibraryPaths();
    }

    public function testJsAssetForwardSlashException(): void
    {
        static::expectException(\LogicException::class);
        static::expectExceptionMessage('Path must not begin with forward-slash');
        new Asset\Js('/foo.js');
    }

    public function testCss(): void
    {
        $css = new Asset\Css('styles.css');
        $css->setPath(realpath(__DIR__ . '/../fixtures/resources/css'));
        static::assertEquals(new AssetLibraryPaths([
            ['css', 'component', realpath(__DIR__ . '/../fixtures/resources/css/styles.css')],
        ]), $css->getLibraryPaths());
    }

    /**
     * @covers \Pinto\Attribute\Asset\Css::getLibraryPaths
     */
    public function testCssFileMissing(): void
    {
        $css = new Asset\Css('does-not-exist.css');
        $css->setPath(realpath(__DIR__ . '/../fixtures/resources/css'));
        static::expectExceptionMessage('File does not exist');
        $css->getLibraryPaths();
    }

    public function testCssAssetForwardSlashException(): void
    {
        static::expectException(\LogicException::class);
        static::expectExceptionMessage('Path must not begin with forward-slash');
        new Asset\Css('/styles.css');
    }

    public function testExternalJs(): void
    {
        $js = new Asset\ExternalJs('https://example.com/foo.js');
        static::assertEquals('https://example.com/foo.js', $js->getUrl());
        static::assertEquals(new AssetLibraryPaths([
            ['js', 'https://example.com/foo.js'],
        ]), $js->getLibraryPaths());
    }

    public function testExternalJsAssetForwardSlashException(): void
    {
        static::expectException(\InvalidArgumentException::class);
        static::expectExceptionMessage('Invalid URL.');
        new Asset\ExternalJs('http://example.com/foo.js');
    }

    public function testExternalCss(): void
    {
        $css = new Asset\ExternalCss('https://example.com/styles.css');
        static::assertEquals('https://example.com/styles.css', $css->getUrl());
        static::assertEquals(new AssetLibraryPaths([
            ['css', 'component', 'https://example.com/styles.css'],
        ]), $css->getLibraryPaths());
    }

    public function testExternalCssAssetForwardSlashException(): void
    {
        static::expectException(\InvalidArgumentException::class);
        static::expectExceptionMessage('Invalid URL.');
        new Asset\ExternalCss('http://example.com/styles.css');
    }
}
