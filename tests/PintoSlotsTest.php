<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pinto\Slots;
use Pinto\tests\fixtures\Lists;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsBasic;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsMissingSlotValue;

/**
 * @coversDefaultClass \Pinto\PintoMapping
 */
final class PintoSlotsTest extends TestCase
{
    public function testSlotsBuild(): void
    {
        $object = new PintoObjectSlotsBasic('Foo!', 12345);
        $build = $object();
        static::assertInstanceOf(Slots\Build::class, $build);
        static::assertEquals('Foo!', $build->pintoGet('text'));
        static::assertEquals(12345, $build->pintoGet('number'));
    }

    public function testSlotsBuildMissingValue(): void
    {
        $object = new PintoObjectSlotsMissingSlotValue('Foo!', 12345);
        static::expectException(Pinto\Exception\Slots\BuildValidation::class);
        static::expectExceptionMessage(sprintf('Build for %s missing values for slot: `number', PintoObjectSlotsMissingSlotValue::class));
        $object();
    }

    public function testDefinitionsSlotsAttrOnObject(): void
    {
        $themeDefinitions = Lists\PintoListSlots::definitions();
        static::assertCount(3, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlots::Slots];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals([
            'text' => [
                'type' => 'string',
                'default' => null,
            ],
            'number' => [
                'type' => 'int',
                'default' => 3,
            ],
        ], $slotsDefinition->slots);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlots::SlotsAttributeOnMethod];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals([
            'foo' => [
                'type' => 'string',
                'default' => null,
            ],
            'arr' => [
                'type' => 'array',
                'default' => [],
            ],
        ], $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrOnList(): void
    {
        $themeDefinitions = Lists\PintoListSlotsOnEnum::definitions();
        static::assertCount(1, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsOnEnum::SlotsOnEnum];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals([
            'fooFromList' => [
                'type' => 'string',
                'default' => null,
            ],
            'number' => [
                'type' => 'int',
                'default' => 4,
            ],
        ], $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrOnListCase(): void
    {
        $themeDefinitions = Lists\PintoListSlotsOnEnumCase::definitions();
        static::assertCount(1, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsOnEnumCase::SlotsOnEnumCase];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals([
            'fooFromListCase' => [
                'type' => 'string',
                'default' => null,
            ],
        ], $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrOnListMethodSpecified(): void
    {
        $themeDefinitions = Lists\PintoListSlotsOnEnumMethodSpecified::definitions();
        static::assertCount(1, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsOnEnumMethodSpecified::SlotsOnEnumMethodSpecified];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals([
            'create' => [
                'type' => 'string',
                'default' => 'from method specified on enum #[Slots]',
            ],
        ], $slotsDefinition->slots);
    }
}
