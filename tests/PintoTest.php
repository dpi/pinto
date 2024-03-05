<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\Js;
use Pinto\Attribute\Build;
use Pinto\Attribute\ThemeDefinition;
use Pinto\Exception\PintoBuildDefinitionMismatch;
use Pinto\Exception\PintoCaseMissingDefinitionAttribute;
use Pinto\Exception\PintoMissingObjectMapping;
use Pinto\PintoMapping;
use Pinto\tests\fixtures\Objects\PintoList;
use Pinto\tests\fixtures\Objects\PintoListMissingDefinition;
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

    public function testPintoCaseMissingDefinitionAttributeException(): void
    {
        static::expectException(PintoCaseMissingDefinitionAttribute::class);
        static::expectExceptionMessage('Case missing `Pinto\tests\fixtures\Objects\PintoListMissingDefinition::Pinto_Missing_Enum');
        PintoListMissingDefinition::themeDefinitions([], '', '', '');
    }

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
              ],
            ],
            'css' => [
              'component' => [
                'tests/fixtures/resources/styles.css' => [
                  'minified' => false,
                  'preprocess' => false,
                  'category' => 'component',
                ],
              ],
            ],
          ],
        ], $themeDefinitions);
    }

    public function testAssets(): void
    {
        $assets = iterator_to_array(PintoList::Pinto_Object->assets());
        static::assertCount(2, $assets);
        static::assertInstanceOf(Js::class, $assets[0]);
        static::assertInstanceOf(Css::class, $assets[1]);
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
}
