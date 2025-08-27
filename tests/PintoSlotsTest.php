<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pinto\DefinitionDiscovery;
use Pinto\Exception\Slots\UnknownValue;
use Pinto\ObjectType\ObjectTypeDiscovery;
use Pinto\Slots;
use Pinto\Slots\SlotList;
use Pinto\tests\fixtures\Etc\SlotEnum;
use Pinto\tests\fixtures\Lists;
use Pinto\tests\fixtures\Lists\PintoListSlots;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsBasic;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsBindPromotedPublic;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsBindPromotedPublicNonConstructor;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsByInheritanceChild;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsByInheritanceChildModifySlots;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsByInheritanceGrandParent;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsExplicit;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsExplicitEnumClass;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsMissingSlotValue;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsRenameChild;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsRenameParent;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsSetInvalidSlot;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsValidationFailurePhpType;

/**
 * @coversDefaultClass \Pinto\PintoMapping
 */
final class PintoSlotsTest extends TestCase
{
    public function testSlotsAttribute(): void
    {
        static::expectException(LogicException::class);
        static::expectExceptionMessage('Using this attribute without named parameters is not supported.');
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
        [1 => $slotsDefinition] = ObjectTypeDiscovery::definitionForThemeObject(PintoObjectSlotsExplicitEnumClass::class, PintoListSlots::PintoObjectSlotsExplicitEnumClass, definitionDiscovery: new DefinitionDiscovery());

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
        [1 => $slotsDefinition] = ObjectTypeDiscovery::definitionForThemeObject(PintoObjectSlotsBindPromotedPublic::class, PintoListSlots::PintoObjectSlotsBindPromotedPublic, definitionDiscovery: new DefinitionDiscovery());

        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'aPublic', fillValueFromThemeObjectClassPropertyWhenEmpty: 'aPublic', validation: Slots\Validation\PhpType::fromString('string')),
            new Slots\Slot(name: 'aPublicAndSetInInvoker', fillValueFromThemeObjectClassPropertyWhenEmpty: 'aPublicAndSetInInvoker', validation: Slots\Validation\PhpType::fromString('string')),
            new Slots\Slot(name: 'aPrivate', validation: Slots\Validation\PhpType::fromString('string')),
        ]), $slotsDefinition->slots);

        $object = new PintoObjectSlotsBindPromotedPublic('the public', 'public but also overridden in invoker', 'the private');
        $build = $object();
        static::assertInstanceOf(Slots\Build::class, $build);
        static::assertEquals('the public', $build->pintoGet('aPublic'));
        static::assertEquals('public value set in invoker', $build->pintoGet('aPublicAndSetInInvoker'));
        static::assertEquals('private value set in invoker', $build->pintoGet('aPrivate'));
    }

    /**
     * @covers \Pinto\Attribute\ObjectType\Slots::__construct
     */
    public function PintoObjectSlotsBindPromotedPublicNonConstructor(): void
    {
        [1 => $slotsDefinition] = ObjectTypeDiscovery::definitionForThemeObject(PintoObjectSlotsBindPromotedPublicNonConstructor::class, PintoListSlots::PintoObjectSlotsBindPromotedPublicNonConstructor, definitionDiscovery: new DefinitionDiscovery());

        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'foo', fillValueFromThemeObjectClassPropertyWhenEmpty: 'foo'),
            new Slots\Slot(name: 'bar', fillValueFromThemeObjectClassPropertyWhenEmpty: 'bar'),
        ]), $slotsDefinition->slots);

        $object = PintoObjectSlotsBindPromotedPublicNonConstructor::actualEntrypoint('text', 'text', 'text', 'text', 'text', 'text');
        $build = $object();
        static::assertInstanceOf(Slots\Build::class, $build);
        static::assertEquals('FOO!', $build->pintoGet('foo'));
        static::assertEquals('BAR!', $build->pintoGet('bar'));
    }

    public function testPintoObjectSlotsBindPromotedPublicWithDefinedSlots(): void
    {
        static::expectException(Pinto\Exception\PintoThemeDefinition::class);
        static::expectExceptionMessage('Slots must use reflection (no explicitly defined `$slots`) when promoted properties bind is on.');
        ObjectTypeDiscovery::definitionForThemeObject(Pinto\tests\fixtures\Objects\Faulty\PintoObjectSlotsBindPromotedPublicWithDefinedSlots::class, Lists\PintoFaultyList::PintoObjectSlotsBindPromotedPublicWithDefinedSlots, definitionDiscovery: new DefinitionDiscovery());
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

    //    /**
    //     * @covers \Pinto\Attribute\ObjectType\Slots::validateBuild
    //     * @covers \Pinto\Exception\Slots\BuildValidation::validation
    //     */
    //    public function testSlotsBuildMissingValueValidationFailurePhpType(): void
    //    {
    //        [1 => $slotsDefinition] = ObjectTypeDiscovery::definitionForThemeObject(PintoObjectSlotsValidationFailurePhpType::class, PintoListSlots::PintoObjectSlotsValidationFailurePhpType, new DefinitionDiscovery());
    //
    //        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
    //        static::assertEquals(new SlotList([
    //            new Slots\Slot(name: 'number', validation: Slots\Validation\PhpType::fromString('int')),
    //        ]), $slotsDefinition->slots);
    //
    //        $object = new PintoObjectSlotsValidationFailurePhpType(123);
    //        static::expectException(Pinto\Exception\Slots\BuildValidation::class);
    //        static::expectExceptionMessage(sprintf('Build for %s failed validation: `number` expects `int`, but got `string`', PintoObjectSlotsValidationFailurePhpType::class));
    //        $object();
    //    }

    /**
     * Tests no constructor is required when #[Slots(slots)] is provided.
     *
     * Issue would normally expose itself during discovery.
     *
     * @see Pinto\Attribute\ObjectType\Slots::getDefinition
     */
    public function testSlotsNoConstructor(): void
    {
        $definitions = PintoListSlots::definitions(new DefinitionDiscovery());
        // Assert anything (no exception thrown):
        static::assertGreaterThan(0, count($definitions));
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
        $themeDefinitions = PintoListSlots::definitions(new DefinitionDiscovery());
        static::assertCount(9, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[PintoListSlots::Slots];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'text', validation: Slots\Validation\PhpType::fromString('string')),
            new Slots\Slot(name: 'number', defaultValue: 3, validation: Slots\Validation\PhpType::fromString('int')),
        ]), $slotsDefinition->slots);

        $slotsDefinition = $themeDefinitions[PintoListSlots::SlotsAttributeOnMethod];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'foo', defaultValue: null, validation: Slots\Validation\PhpType::fromString('?string')),
            new Slots\Slot(name: 'arr', defaultValue: [], validation: Slots\Validation\PhpType::fromString('?array')),
        ]), $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrOnList(): void
    {
        $themeDefinitions = Lists\PintoListSlotsOnEnum::definitions(new DefinitionDiscovery());
        static::assertCount(1, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsOnEnum::SlotsOnEnum];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'fooFromList', validation: Slots\Validation\PhpType::fromString('string')),
            new Slots\Slot(name: 'number', defaultValue: 4, validation: Slots\Validation\PhpType::fromString('int')),
        ]), $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrOnListCase(): void
    {
        $themeDefinitions = Lists\PintoListSlotsOnEnumCase::definitions(new DefinitionDiscovery());
        static::assertCount(1, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsOnEnumCase::SlotsOnEnumCase];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'fooFromListCase', validation: Slots\Validation\PhpType::fromString('string')),
        ]), $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrByInheritance(): void
    {
        $definitionDiscovery = new DefinitionDiscovery();
        $definitionDiscovery[PintoObjectSlotsByInheritanceChild::class] = Lists\PintoListSlotsByInheritance::SlotsByInheritanceChild;
        $definitionDiscovery[PintoObjectSlotsByInheritanceGrandParent::class] = Lists\PintoListSlotsByInheritance::SlotsByInheritanceGrandParent;
        $themeDefinitions = Lists\PintoListSlotsByInheritance::definitions($definitionDiscovery);
        static::assertCount(3, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsByInheritance::SlotsByInheritanceChild];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'fooFromGrandParent', validation: Slots\Validation\PhpType::fromString('string')),
        ]), $slotsDefinition->slots);
    }

    /**
     * @covers \Pinto\Slots\Attribute\ModifySlots::__construct
     */
    public function testModifySlotsAttributeNamedParameters(): void
    {
        static::expectException(LogicException::class);
        static::expectExceptionMessage('Using this attribute without named parameters is not supported.');
        new Slots\Attribute\ModifySlots('');
    }

    /**
     * @covers \Pinto\Slots\Attribute\ModifySlots::__construct
     */
    public function testModifySlotsAttributeAddMissingSlots(): void
    {
        static::expectException(LogicException::class);
        static::expectExceptionMessage('Slots must be added.');
        new Slots\Attribute\ModifySlots(add: []);
    }

    public function testDefinitionsSlotsAttrByInheritanceModifiedSlots(): void
    {
        $definitionDiscovery = new DefinitionDiscovery();
        $definitionDiscovery[PintoObjectSlotsByInheritanceChild::class] = Lists\PintoListSlotsByInheritance::SlotsByInheritanceChild;
        $definitionDiscovery[PintoObjectSlotsByInheritanceChildModifySlots::class] = Lists\PintoListSlotsByInheritance::PintoObjectSlotsByInheritanceChildModifySlots;
        $definitionDiscovery[PintoObjectSlotsByInheritanceGrandParent::class] = Lists\PintoListSlotsByInheritance::SlotsByInheritanceGrandParent;
        $themeDefinitions = Lists\PintoListSlotsByInheritance::definitions($definitionDiscovery);
        static::assertCount(3, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsByInheritance::PintoObjectSlotsByInheritanceChildModifySlots];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'fooFromGrandParent', validation: Slots\Validation\PhpType::fromString('string')),
            new Slots\Slot(name: 'new_slot'),
        ]), $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrByInheritanceGrandParentUnregistered(): void
    {
        // It the parent isn't registered to an enum, no object type is determined.
        $definitionDiscovery = new DefinitionDiscovery();
        // Normally parent is set here.
        $definitionDiscovery[PintoObjectSlotsByInheritanceChild::class] = Lists\PintoListSlotsByInheritance::SlotsByInheritanceChild;

        static::expectException(Pinto\Exception\PintoIndeterminableObjectType::class);
        Lists\PintoListSlotsByInheritance::definitions($definitionDiscovery);
    }

    public function testDefinitionsSlotsAttrOnListMethodSpecified(): void
    {
        $themeDefinitions = Lists\PintoListSlotsOnEnumMethodSpecified::definitions(new DefinitionDiscovery());
        static::assertCount(1, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsOnEnumMethodSpecified::SlotsOnEnumMethodSpecified];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'create', defaultValue: 'from method specified on enum #[Slots]', validation: Slots\Validation\PhpType::fromString('?string')),
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
        $definitionDiscovery = new DefinitionDiscovery();
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
