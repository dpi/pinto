<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\Js;
use Pinto\Library\LibraryBuilder;
use Pinto\tests\fixtures\Lists\PintoListNoDefinitions;

use function Safe\realpath;

/**
 * Tests pinto enums where cases do not have definitions.
 *
 * @see PintoListNoDefinitions
 */
final class PintoNoDefinitionsTest extends TestCase
{
    /**
     * @covers \Pinto\tests\PintoTestUtility::definitions
     */
    public function testNoThemeDefinitions(): void
    {
        static::assertCount(0, PintoTestUtility::definitions(PintoListNoDefinitions::class, new \Pinto\DefinitionDiscovery()));
    }

    /**
     * @covers \Pinto\List\ObjectListTrait::assets
     * @covers \Pinto\Library\LibraryBuilder::expandLibraryPaths
     */
    public function testAssets(): void
    {
        static::assertEquals([
            new Css('styles.css'),
            new Js('app.js'),
        ], \iterator_to_array(PintoListNoDefinitions::No_Definition_With_Assets->assets()));

        static::assertEquals([
            [(new Css('styles.css'))->setPath(realpath(__DIR__ . '/fixtures/resources/css/')), [
                'css', 'component', realpath(__DIR__ . '/fixtures/resources/css/styles.css'),
            ]],
            [(new Js('app.js'))->setPath(realpath(__DIR__ . '/fixtures/resources/javascript/')), [
                'js', realpath(__DIR__ . '/fixtures/resources/javascript/app.js'),
            ]],
        ], iterator_to_array(LibraryBuilder::expandLibraryPaths(PintoListNoDefinitions::No_Definition_With_Assets)));
    }
}
