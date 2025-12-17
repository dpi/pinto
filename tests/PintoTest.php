<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\ExternalCss;
use Pinto\Attribute\Asset\ExternalJs;
use Pinto\Attribute\Asset\Js;
use Pinto\Attribute\Build;
use Pinto\Exception\Slots\BuildValidation;
use Pinto\Library\LibraryBuilder;
use Pinto\Slots;
use Pinto\tests\fixtures\Lists\PintoBuildOverrideList;
use Pinto\tests\fixtures\Lists\PintoList;
use Pinto\tests\fixtures\Objects\Build\PintoBuildOverrideObject;
use Pinto\tests\fixtures\Objects\Extends\PintoObjectExtends1;
use Pinto\tests\fixtures\Objects\Extends\PintoObjectExtends2;
use Pinto\tests\fixtures\Objects\PintoObject;
use Pinto\tests\fixtures\Objects\PintoObjectBuildDefinitionMismatch;

use function Safe\realpath;

final class PintoTest extends TestCase
{
    /**
     * Tests where an object build method doesn't fulfill required keys.
     *
     * @covers \Pinto\Object\ObjectTrait::pintoBuild
     */
    public function testPintoBuildDefinitionMismatchException(): void
    {
        static::expectException(BuildValidation::class);
        static::expectExceptionMessage('Build for Pinto\tests\fixtures\Objects\PintoObjectBuildDefinitionMismatch missing values for slot: `test_variable`');
        $object = PintoObjectBuildDefinitionMismatch::create('Foo bar!');
        $object();
    }

    /**
     * @covers \Pinto\tests\PintoTestUtility::definitions
     */
    public function testThemeDefinitions(): void
    {
        $themeDefinitions = PintoTestUtility::definitions(PintoList::class, new \Pinto\DefinitionDiscovery());
        static::assertCount(2, $themeDefinitions);

        $definition1 = $themeDefinitions[PintoList::Pinto_Object];
        static::assertInstanceOf(Slots\Definition::class, $definition1);
        static::assertEquals(new Slots\Definition(
            slots: new Slots\SlotList([
                new Slots\Slot(name: 'test_variable', origin: Slots\Origin\StaticallyDefined::create(data: 'test_variable')),
            ]),
            renameSlots: Slots\RenameSlots::create(),
        ), $definition1);

        $definition2 = $themeDefinitions[PintoList::Pinto_Object_Attributes];
        static::assertInstanceOf(Slots\Definition::class, $definition2);
        static::assertEquals(new Slots\Definition(
            slots: new Slots\SlotList([
                new Slots\Slot(name: 'test_variable', origin: Slots\Origin\StaticallyDefined::create(data: 'test_variable')),
            ]),
            renameSlots: Slots\RenameSlots::create(),
        ), $definition2);
    }

    /**
     * @covers \Pinto\Library\LibraryBuilder::expandLibraryPaths
     */
    public function testLibraries(): void
    {
        static::assertEquals([
            [(new Css('styles.css'))->setPath(realpath(__DIR__ . '/fixtures/resources/css/')), [
                'css', 'component', realpath(__DIR__ . '/fixtures/resources/css/styles.css'),
            ]],
            [(new Js('app.js'))->setPath(realpath(__DIR__ . '/fixtures/resources/javascript/')), [
                'js', realpath(__DIR__ . '/fixtures/resources/javascript/app.js'),
            ]],
            [new ExternalJs('https://example.com/path.js'), [
                'js', 'https://example.com/path.js',
            ]],
            [new ExternalCss('https://example.com/path.css'), [
                'css', 'component', 'https://example.com/path.css',
            ]],
        ], iterator_to_array(LibraryBuilder::expandLibraryPaths(PintoList::Pinto_Object)));

        static::assertEquals([
            [(new Css('styles.css', attributes: ['defer' => true]))->setPath(realpath(__DIR__ . '/fixtures/resources/css/')), [
                'css', 'component', realpath(__DIR__ . '/fixtures/resources/css/styles.css'),
            ]],
            [(new Js('app.js', attributes: ['defer' => true]))->setPath(realpath(__DIR__ . '/fixtures/resources/javascript/')), [
                'js', realpath(__DIR__ . '/fixtures/resources/javascript/app.js'),
            ]],
            [new ExternalJs('https://example.com/path.js', attributes: ['defer' => true]), [
                'js', 'https://example.com/path.js',
            ]],
            [new ExternalCss('https://example.com/path.css', attributes: ['defer' => true]), [
                'css', 'component', 'https://example.com/path.css',
            ]],
        ], iterator_to_array(LibraryBuilder::expandLibraryPaths(PintoList::Pinto_Object_Attributes)));
    }

    public function testAssets(): void
    {
        $assets = iterator_to_array(PintoList::Pinto_Object->assets());
        static::assertCount(4, $assets);
        static::assertInstanceOf(Css::class, $assets[0]);
        static::assertInstanceOf(Js::class, $assets[1]);
        static::assertInstanceOf(ExternalJs::class, $assets[2]);
        static::assertInstanceOf(ExternalCss::class, $assets[3]);
    }

    public function testObject(): void
    {
        $object = PintoObject::create('Foo bar!');
        $result = $object();
        static::assertInstanceOf(Slots\Build::class, $result);
        $this::assertEquals('Foo bar!', $result->pintoGet('test_variable'));
    }

    public function testBuildMethodForThemeObject(): void
    {
        static::assertEquals(
            '__invoke',
            Build::buildMethodForThemeObject(PintoObject::class),
        );
    }

    public function testBuildOverride(): void
    {
        $object = PintoBuildOverrideObject::create('Foo');
        $result = $object();
        static::assertInstanceOf(Slots\Build::class, $result);
        static::assertEquals(
            PintoBuildOverrideObject::class . ' set by ' . PintoBuildOverrideList::class,
            $result->pintoGet('build_context_from_list'),
        );
    }

    /**
     * @covers \Pinto\Object\ObjectTrait::pintoBuild
     */
    public function testObjectExtends(): void
    {
        $object = PintoObjectExtends1::create('Foo bar!');
        $result = $object();
        static::assertInstanceOf(Slots\Build::class, $result);
        $this::assertEquals('Foo bar!', $result->pintoGet('text'));

        $object = PintoObjectExtends2::create('Fizz buzz!');
        $result = $object();
        static::assertInstanceOf(Slots\Build::class, $result);
        $this::assertEquals('Fizz buzz!', $result->pintoGet('text'));
    }
}
