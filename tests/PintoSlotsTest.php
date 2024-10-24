<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pinto\Exception\Slots\UnknownValue;
use Pinto\Slots;
use Pinto\Slots\SlotList;
use Pinto\tests\fixtures\Etc\SlotEnum;
use Pinto\tests\fixtures\Lists;
use Pinto\tests\fixtures\Lists\PintoListSlots;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsBasic;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsExplicit;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsExplicitEnumClass;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsMissingSlotValue;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsSetInvalidSlot;

/**
 * @coversDefaultClass \Pinto\PintoMapping
 */
final class PintoSlotsTest extends TestCase
{
    public function testSlotsAttribute(): void
    {
        static::expectException(LogicException::class);
        new Pinto\Attribute\ObjectType\Slots('');
    }

    public function testSlotsBuild(): void
    {
        $object = new PintoObjectSlotsBasic('Foo!', 12345);
        $build = $object();
        static::assertInstanceOf(Slots\Build::class, $build);
        static::assertEquals('Foo!', $build->pintoGet('text'));
        static::assertEquals(12345, $build->pintoGet('number'));
    }

    public function testSlotsSetInvalidSlot(): void
    {
        $object = new PintoObjectSlotsSetInvalidSlot();
        static::expectException(UnknownValue::class);
        static::expectExceptionMessage(sprintf('Unknown slot `%s::%s`', SlotEnum::class, SlotEnum::Slot1->name));
        $object();
    }

    public function testSlotsExplicit(): void
    {
        $object = new PintoObjectSlotsExplicit();
        $build = $object();
        static::assertInstanceOf(Slots\Build::class, $build);
        static::assertEquals('Some text', $build->pintoGet('text'));
        static::assertEquals(12345, $build->pintoGet('number'));
    }

    public function testSlotsExplicitEnums(): void
    {
        $object = new Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsExplicitEnums();
        $build = $object();
        static::assertInstanceOf(Slots\Build::class, $build);
        static::assertEquals('Slot One', $build->pintoGet(SlotEnum::Slot1));
        static::assertEquals(23456, $build->pintoGet(SlotEnum::Slot2));
        static::expectException(UnknownValue::class);
        static::expectExceptionMessage(sprintf('Unknown slot `%s::%s`', SlotEnum::class, SlotEnum::Slot3->name));
        $build->pintoGet(SlotEnum::Slot3);
    }

    /**
     * @covers \Pinto\Attribute\ObjectType\Slots::__construct
     */
    public function testSlotsExplicitEnumClass(): void
    {
        // Call \Pinto\ObjectType\ObjectTypeDiscovery::definitionForThemeObject
        // directly since pintoMapping won't execute enum->cases expansion..
        [1 => $slotsDefinition] = Pinto\ObjectType\ObjectTypeDiscovery::definitionForThemeObject(PintoObjectSlotsExplicitEnumClass::class, PintoListSlots::PintoObjectSlotsExplicitEnumClass);

        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: SlotEnum::Slot1),
            new Slots\Slot(name: SlotEnum::Slot2),
            new Slots\Slot(name: SlotEnum::Slot3),
        ]), $slotsDefinition->slots);
    }

    public function testSlotsExplicitIgnoresReflection(): void
    {
        $object = new Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsExplicitIgnoresReflection('Should be ignored', 999);
        $build = $object();
        static::assertInstanceOf(Slots\Build::class, $build);
        static::assertEquals('Some text', $build->pintoGet('text'));
        static::assertEquals(12345, $build->pintoGet('number'));
    }

    public function testSlotsBuildMissingValue(): void
    {
        $object = new PintoObjectSlotsMissingSlotValue('Foo!', 12345);
        static::expectException(Pinto\Exception\Slots\BuildValidation::class);
        static::expectExceptionMessage(sprintf('Build for %s missing values for slot: `number', PintoObjectSlotsMissingSlotValue::class));
        $object();
    }

    public function testSlotsBuildMissingValueWithDefault(): void
    {
        $object = new Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsMissingSlotValueWithDefault('Foo!');
        $build = $object();
        static::assertInstanceOf(Slots\Build::class, $build);
        static::assertEquals('Foo!', $build->pintoGet('text'));
        // '3' comes from the entrypoint (constructor).
        static::assertEquals(3, $build->pintoGet('number'));
    }

    public function testDefinitionsSlotsAttrOnObject(): void
    {
        $themeDefinitions = PintoListSlots::definitions();
        static::assertCount(5, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[PintoListSlots::Slots];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'text'),
            new Slots\Slot(name: 'number', defaultValue: 3),
        ]), $slotsDefinition->slots);

        $slotsDefinition = $themeDefinitions[PintoListSlots::SlotsAttributeOnMethod];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'foo', defaultValue: null),
            new Slots\Slot(name: 'arr', defaultValue: []),
        ]), $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrOnList(): void
    {
        $themeDefinitions = Lists\PintoListSlotsOnEnum::definitions();
        static::assertCount(1, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsOnEnum::SlotsOnEnum];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'fooFromList'),
            new Slots\Slot(name: 'number', defaultValue: 4),
        ]), $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrOnListCase(): void
    {
        $themeDefinitions = Lists\PintoListSlotsOnEnumCase::definitions();
        static::assertCount(1, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsOnEnumCase::SlotsOnEnumCase];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'fooFromListCase'),
        ]), $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrOnListMethodSpecified(): void
    {
        $themeDefinitions = Lists\PintoListSlotsOnEnumMethodSpecified::definitions();
        static::assertCount(1, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsOnEnumMethodSpecified::SlotsOnEnumMethodSpecified];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'create', defaultValue: 'from method specified on enum #[Slots]'),
        ]), $slotsDefinition->slots);
    }

    public function testSlotAttribute(): void
    {
        $attr = new Pinto\Attribute\ObjectType\Slots(slots: [
            new Slots\Slot(name: 'foo'),
            'bar',
        ]);

        static::assertEquals([
            new Slots\Slot(name: 'foo'),
            new Slots\Slot(name: 'bar'),
        ], $attr->slots->toArray());
    }

    public function testSlotNamedParameters(): void
    {
        static::expectException(LogicException::class);
        static::expectExceptionMessage('Using this attribute without named parameters is not supported.');
        new Slots\Slot('slotname', '', 'defaultvalue');
    }

    public function testSlotAttributeNamedParameters(): void
    {
        static::expectException(LogicException::class);
        static::expectExceptionMessage('Using this attribute without named parameters is not supported.');
        new Pinto\Attribute\ObjectType\Slots('');
    }
}
