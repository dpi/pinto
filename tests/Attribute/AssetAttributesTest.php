<?php

declare(strict_types=1);

namespace Pinto\tests\Attribute;

use PHPUnit\Framework\TestCase;
use Pinto\Attribute\Asset;

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
        $js->setPath('/javascript/directory');
        static::assertEquals(['js', '/javascript/directory/foo.js'], $js->getLibraryPath());
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
        $css->setPath('/css/directory');
        static::assertEquals(['css', 'component', '/css/directory/styles.css'], $css->getLibraryPath());
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
        static::assertEquals(['js', 'https://example.com/foo.js'], $js->getLibraryPath());
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
        static::assertEquals(['css', 'component', 'https://example.com/styles.css'], $css->getLibraryPath());
    }

    public function testExternalCssAssetForwardSlashException(): void
    {
        static::expectException(\InvalidArgumentException::class);
        static::expectExceptionMessage('Invalid URL.');
        new Asset\ExternalCss('http://example.com/styles.css');
    }
}
