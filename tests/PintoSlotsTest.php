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
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsBindPromotedPublic;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsByInheritanceChild;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsByInheritanceGrandParent;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsExplicit;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsExplicitEnumClass;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsMissingSlotValue;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsRenameChild;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsRenameParent;
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
        [1 => $slotsDefinition] = Pinto\ObjectType\ObjectTypeDiscovery::definitionForThemeObject(PintoObjectSlotsExplicitEnumClass::class, PintoListSlots::PintoObjectSlotsExplicitEnumClass, definitionDiscovery: new Pinto\DefinitionDiscovery());

        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: SlotEnum::Slot1),
            new Slots\Slot(name: SlotEnum::Slot2),
            new Slots\Slot(name: SlotEnum::Slot3),
        ]), $slotsDefinition->slots);
    }

    /**
     * @covers \Pinto\Attribute\ObjectType\Slots::__construct
     */
    public function testPintoObjectSlotsBindPromotedPublic(): void
    {
        [1 => $slotsDefinition] = Pinto\ObjectType\ObjectTypeDiscovery::definitionForThemeObject(PintoObjectSlotsBindPromotedPublic::class, PintoListSlots::PintoObjectSlotsBindPromotedPublic, definitionDiscovery: new Pinto\DefinitionDiscovery());

        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'aPublic', fillValueFromThemeObjectClassPropertyWhenEmpty: 'aPublic'),
            new Slots\Slot(name: 'aPublicAndSetInInvoker', fillValueFromThemeObjectClassPropertyWhenEmpty: 'aPublicAndSetInInvoker'),
            new Slots\Slot(name: 'aPrivate'),
        ]), $slotsDefinition->slots);

        $object = new PintoObjectSlotsBindPromotedPublic('the public', 'public but also overridden in invoker', 'the private');
        $build = $object();
        static::assertInstanceOf(Slots\Build::class, $build);
        static::assertEquals('the public', $build->pintoGet('aPublic'));
        static::assertEquals('public value set in invoker', $build->pintoGet('aPublicAndSetInInvoker'));
        static::assertEquals('private value set in invoker', $build->pintoGet('aPrivate'));
    }

    public function testPintoObjectSlotsBindPromotedPublicWithDefinedSlots(): void
    {
        static::expectException(Pinto\Exception\PintoThemeDefinition::class);
        static::expectExceptionMessage('Slots must use reflection (no explicitly defined `$slots`) when promoted properties bind is on.');
        Pinto\ObjectType\ObjectTypeDiscovery::definitionForThemeObject(Pinto\tests\fixtures\Objects\Faulty\PintoObjectSlotsBindPromotedPublicWithDefinedSlots::class, Lists\PintoFaultyList::PintoObjectSlotsBindPromotedPublicWithDefinedSlots, definitionDiscovery: new Pinto\DefinitionDiscovery());
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
        $themeDefinitions = PintoListSlots::definitions(new Pinto\DefinitionDiscovery());
        static::assertCount(6, $themeDefinitions);

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
        $themeDefinitions = Lists\PintoListSlotsOnEnum::definitions(new Pinto\DefinitionDiscovery());
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
        $themeDefinitions = Lists\PintoListSlotsOnEnumCase::definitions(new Pinto\DefinitionDiscovery());
        static::assertCount(1, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsOnEnumCase::SlotsOnEnumCase];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'fooFromListCase'),
        ]), $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrByInheritance(): void
    {
        $definitionDiscovery = new Pinto\DefinitionDiscovery();
        $definitionDiscovery[PintoObjectSlotsByInheritanceChild::class] = Lists\PintoListSlotsByInheritance::SlotsByInheritanceChild;
        $definitionDiscovery[PintoObjectSlotsByInheritanceGrandParent::class] = Lists\PintoListSlotsByInheritance::SlotsByInheritanceGrandParent;
        $themeDefinitions = Lists\PintoListSlotsByInheritance::definitions($definitionDiscovery);
        static::assertCount(2, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsByInheritance::SlotsByInheritanceChild];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'fooFromGrandParent'),
        ]), $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrByInheritanceGrandParentUnregistered(): void
    {
        // It the parent isn't registered to an enum, no object type is determined.
        $definitionDiscovery = new Pinto\DefinitionDiscovery();
        // Normally parent is set here.
        $definitionDiscovery[PintoObjectSlotsByInheritanceChild::class] = Lists\PintoListSlotsByInheritance::SlotsByInheritanceChild;

        static::expectException(Pinto\Exception\PintoIndeterminableObjectType::class);
        Lists\PintoListSlotsByInheritance::definitions($definitionDiscovery);
    }

    public function testDefinitionsSlotsAttrOnListMethodSpecified(): void
    {
        $themeDefinitions = Lists\PintoListSlotsOnEnumMethodSpecified::definitions(new Pinto\DefinitionDiscovery());
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

    /**
     * @covers \Pinto\Slots\Attribute\RenameSlot
     * @covers \Pinto\Slots\RenameSlots
     */
    public function testRenameSlots(): void
    {
        $definitionDiscovery = new Pinto\DefinitionDiscovery();
        $definitionDiscovery[PintoObjectSlotsRenameParent::class] = Lists\PintoListSlotsRename::SlotsRenameParent;
        $definitionDiscovery[PintoObjectSlotsRenameChild::class] = Lists\PintoListSlotsRename::SlotsRenameChild;
        $themeDefinitions = Lists\PintoListSlotsRename::definitions($definitionDiscovery);
        static::assertCount(2, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsRename::SlotsRenameChild];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'slotFromParentUnrenamed'),
            new Slots\Slot(name: 'stringFromParentThatWillBeRenamed'),
            new Slots\Slot(name: SlotEnum::Slot1),
        ]), $slotsDefinition->slots);

        $expectedRenameSlots = Slots\RenameSlots::create();
        $expectedRenameSlots->add(new Slots\Attribute\RenameSlot('stringFromParentThatWillBeRenamed', 'stringRenamed'));
        $expectedRenameSlots->add(new Slots\Attribute\RenameSlot(SlotEnum::Slot1, 'enumRenamed'));
        static::assertEquals($expectedRenameSlots, $slotsDefinition->renameSlots);

        static::assertNull($expectedRenameSlots->renamesTo('unknown slot'));
        static::assertNull($expectedRenameSlots->renamesTo(SlotEnum::Slot2));
        static::assertEquals('stringRenamed', $expectedRenameSlots->renamesTo('stringFromParentThatWillBeRenamed'));
        static::assertEquals('enumRenamed', $expectedRenameSlots->renamesTo(SlotEnum::Slot1));
    }
}
