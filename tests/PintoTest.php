<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\ExternalCss;
use Pinto\Attribute\Asset\ExternalJs;
use Pinto\Attribute\Asset\Js;
use Pinto\Attribute\Build;
use Pinto\Attribute\ThemeDefinition;
use Pinto\Exception\PintoBuildDefinitionMismatch;
use Pinto\Exception\PintoMissingObjectMapping;
use Pinto\PintoMapping;
use Pinto\tests\fixtures\Lists\PintoList;
use Pinto\tests\fixtures\Objects\Extends\PintoObjectExtends1;
use Pinto\tests\fixtures\Objects\Extends\PintoObjectExtends2;
use Pinto\tests\fixtures\Objects\PintoBuildOverrideObject;
use Pinto\tests\fixtures\Objects\PintoObject;
use Pinto\tests\fixtures\Objects\PintoObjectBuildDefinitionMismatch;

/**
 * @coversDefaultClass \Pinto\PintoMapping
 */
final class PintoTest extends TestCase
{
    public function testPintoMapping(): void
    {
        $pintoMapping = new PintoMapping([], [], [], []);
        static::expectException(PintoMissingObjectMapping::class);
        $pintoMapping->getBuildInvoker(PintoObject::class);
    }

    /**
     * Tests where an object build method doesn't fulfill required keys.
     *
     * @covers \Pinto\Object\ObjectTrait::pintoBuild
     * @covers \Pinto\Exception\PintoBuildDefinitionMismatch
     */
    public function testPintoBuildDefinitionMismatchException(): void
    {
        static::expectException(PintoBuildDefinitionMismatch::class);
        static::expectExceptionMessage('Build for Pinto\tests\fixtures\Objects\PintoObjectBuildDefinitionMismatch is missing keys: #test_variable');
        $object = PintoObjectBuildDefinitionMismatch::create('Foo bar!');
        static::assertEquals([], $object());
    }

    /**
     * @covers \Pinto\List\ObjectListInterface::themeDefinitions
     * @covers \Pinto\List\ObjectListTrait::themeDefinitions
     */
    public function testThemeDefinitions(): void
    {
        $themeDefinitions = PintoList::themeDefinitions([], '', '', '');
        static::assertEquals([
            'object_test' => [
                'variables' => [
                    'test_variable' => null,
                ],
                'path' => 'tests/fixtures/resources',
                'template' => 'object-test',
            ],
            'object_test_attributes' => [
                'variables' => [
                    'test_variable' => null,
                ],
                'path' => 'tests/fixtures/resources',
                'template' => 'object-test-attributes',
            ],
        ], $themeDefinitions);
    }

    /**
     * @covers \Pinto\List\ObjectListTrait::libraries
     */
    public function testLibraries(): void
    {
        $themeDefinitions = PintoList::libraries();
        static::assertEquals([
            'object_test' => [
                'js' => [
                    'tests/fixtures/resources/app.js' => [
                        'minified' => false,
                        'preprocess' => false,
                        'attributes' => [],
                    ],
                    'https://example.com/path.js' => [
                        'external' => true,
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
                        'https://example.com/path.css' => [
                            'external' => true,
                            'attributes' => [],
                        ],
                    ],
                ],
            ],
            'object_test_attributes' => [
                'js' => [
                    'tests/fixtures/resources/app.js' => [
                        'minified' => false,
                        'preprocess' => false,
                        'attributes' => ['defer' => true],
                    ],
                    'https://example.com/path.js' => [
                        'external' => true,
                        'attributes' => ['defer' => true],
                    ],
                ],
                'css' => [
                    'component' => [
                        'tests/fixtures/resources/styles.css' => [
                            'minified' => false,
                            'preprocess' => false,
                            'category' => 'component',
                            'attributes' => ['defer' => true],
                        ],
                        'https://example.com/path.css' => [
                            'external' => true,
                            'attributes' => ['defer' => true],
                        ],
                    ],
                ],
            ],
        ], $themeDefinitions);
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
        $this::assertEquals([
            '#theme' => 'object_test',
            '#attached' => [
                'library' => [
                    'pinto/object_test',
                ],
            ],
        ], $object());
    }

    public function testBuildMethodForThemeObject(): void
    {
        static::assertEquals(
            '__invoke',
            Build::buildMethodForThemeObject(PintoObject::class),
        );
    }

    public function testThemeDefinitionForThemeObject(): void
    {
        static::assertEquals(
            [
                'variables' => [
                    'test_variable' => null,
                ],
            ],
            ThemeDefinition::themeDefinitionForThemeObject(PintoObject::class),
        );
    }

    public function testBuildOverride(): void
    {
        $object = PintoBuildOverrideObject::create('Foo');
        static::assertEquals(
            PintoBuildOverrideObject::class,
            $object()['build_context_from_list'],
        );
    }

    /**
     * @covers \Pinto\Object\ObjectTrait::pintoBuild
     */
    public function testObjectExtends(): void
    {
        $object = PintoObjectExtends1::create('Foo bar!');
        $this::assertEquals([
            '#theme' => 'extends1',
            '#attached' => [
                'library' => [
                    'pinto/extends1',
                ],
            ],
        ], $object());

        $object = PintoObjectExtends2::create('Fizz buzz!');
        $this::assertEquals([
            // Ensures the enum is matched and the correct meta information is merged into the build.
            '#theme' => 'extends2',
            '#attached' => [
                'library' => [
                    'pinto/extends2',
                ],
            ],
        ], $object());
    }
}
