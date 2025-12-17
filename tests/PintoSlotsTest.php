<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Exception\PintoObjectTypeDefinition;
use Pinto\Exception\Slots\UnknownValue;
use Pinto\Slots;
use Pinto\Slots\SlotList;
use Pinto\tests\fixtures\Etc\SlotEnum;
use Pinto\tests\fixtures\Lists;
use Pinto\tests\fixtures\Lists\PintoListSlots;
use Pinto\tests\fixtures\Objects\Faulty\PintoObjectSlotsBindPromotedPublicWithDefinedSlots;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsAttributeOnMethod;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsBasic;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsBindPromotedPublic;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsBindPromotedPublicNonConstructor;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsByInheritanceChild;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsByInheritanceChildModifySlots;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsByInheritanceGrandParent;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsExplicit;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsExplicitEnumClass;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsExplicitEnums;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsExplicitIgnoresReflection;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsFromList;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsFromListCase;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsFromListMethodSpecified;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsMissingSlotValue;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsMissingSlotValueWithDefault;
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
        static::expectException(\LogicException::class);
        static::expectExceptionMessage('Using this attribute without named parameters is not supported.');
        new \Pinto\Attribute\ObjectType\Slots('');
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
        $object = new PintoObjectSlotsExplicitEnums();
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
        [1 => $slotsDefinition] = \Pinto\ObjectType\ObjectTypeDiscovery::definitionForThemeObject(PintoObjectSlotsExplicitEnumClass::class, PintoListSlots::PintoObjectSlotsExplicitEnumClass, definitionDiscovery: new \Pinto\DefinitionDiscovery());

        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: SlotEnum::Slot1, origin: Slots\Origin\EnumCase::createFromEnum(SlotEnum::Slot1)),
            new Slots\Slot(name: SlotEnum::Slot2, origin: Slots\Origin\EnumCase::createFromEnum(SlotEnum::Slot2)),
            new Slots\Slot(name: SlotEnum::Slot3, origin: Slots\Origin\EnumCase::createFromEnum(SlotEnum::Slot3)),
        ]), $slotsDefinition->slots);
    }

    /**
     * @covers \Pinto\Attribute\ObjectType\Slots::__construct
     */
    public function testPintoObjectSlotsBindPromotedPublic(): void
    {
        [1 => $slotsDefinition] = \Pinto\ObjectType\ObjectTypeDiscovery::definitionForThemeObject(PintoObjectSlotsBindPromotedPublic::class, PintoListSlots::PintoObjectSlotsBindPromotedPublic, definitionDiscovery: new \Pinto\DefinitionDiscovery());

        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'aPublic', origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsBindPromotedPublic::class, '__construct'], 'aPublic')), fillValueFromThemeObjectClassPropertyWhenEmpty: 'aPublic'),
            new Slots\Slot(name: 'aPublicAndSetInInvoker', origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsBindPromotedPublic::class, '__construct'], 'aPublicAndSetInInvoker')), fillValueFromThemeObjectClassPropertyWhenEmpty: 'aPublicAndSetInInvoker'),
            new Slots\Slot(name: 'aPrivate', origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsBindPromotedPublic::class, '__construct'], 'aPrivate'))),
            new Slots\Slot(name: 'unionType', origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsBindPromotedPublic::class, '__construct'], 'unionType')), fillValueFromThemeObjectClassPropertyWhenEmpty: 'unionType'),
        ]), $slotsDefinition->slots);

        $object = new PintoObjectSlotsBindPromotedPublic('the public', 'public but also overridden in invoker', 'the private', 42.0);
        $build = $object();
        static::assertInstanceOf(Slots\Build::class, $build);
        static::assertEquals('the public', $build->pintoGet('aPublic'));
        static::assertEquals('public value set in invoker', $build->pintoGet('aPublicAndSetInInvoker'));
        static::assertEquals('private value set in invoker', $build->pintoGet('aPrivate'));
        static::assertEquals(42.0, $build->pintoGet('unionType'));
    }

    /**
     * @covers \Pinto\Attribute\ObjectType\Slots::__construct
     */
    public function PintoObjectSlotsBindPromotedPublicNonConstructor(): void
    {
        [1 => $slotsDefinition] = \Pinto\ObjectType\ObjectTypeDiscovery::definitionForThemeObject(PintoObjectSlotsBindPromotedPublicNonConstructor::class, PintoListSlots::PintoObjectSlotsBindPromotedPublicNonConstructor, definitionDiscovery: new \Pinto\DefinitionDiscovery());

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
        static::expectException(PintoObjectTypeDefinition::class);
        static::expectExceptionMessage('Slots must use reflection (no explicitly defined `$slots`) when promoted properties bind is on.');
        \Pinto\ObjectType\ObjectTypeDiscovery::definitionForThemeObject(PintoObjectSlotsBindPromotedPublicWithDefinedSlots::class, Lists\PintoFaultyList::PintoObjectSlotsBindPromotedPublicWithDefinedSlots, definitionDiscovery: new \Pinto\DefinitionDiscovery());
    }

    public function testSlotsExplicitIgnoresReflection(): void
    {
        $object = new PintoObjectSlotsExplicitIgnoresReflection('Should be ignored', 999);
        $build = $object();
        static::assertInstanceOf(Slots\Build::class, $build);
        static::assertEquals('Some text', $build->pintoGet('text'));
        static::assertEquals(12345, $build->pintoGet('number'));
    }

    public function testSlotsBuildMissingValue(): void
    {
        $object = new PintoObjectSlotsMissingSlotValue('Foo!', 12345);
        static::expectException(\Pinto\Exception\Slots\BuildValidation::class);
        static::expectExceptionMessage(sprintf('Build for %s missing values for slot: `number', PintoObjectSlotsMissingSlotValue::class));
        $object();
    }

    /**
     * Tests no constructor is required when #[Slots(slots)] is provided.
     *
     * Issue would normally expose itself during discovery.
     *
     * @see \Pinto\Attribute\ObjectType\Slots::getDefinition
     */
    public function testSlotsNoConstructor(): void
    {
        $definitions = PintoTestUtility::definitions(PintoListSlots::class, new \Pinto\DefinitionDiscovery());
        // Assert anything (no exception thrown):
        static::assertGreaterThan(0, count($definitions));
    }

    public function testSlotsBuildMissingValueWithDefault(): void
    {
        $object = new PintoObjectSlotsMissingSlotValueWithDefault('Foo!');
        $build = $object();
        static::assertInstanceOf(Slots\Build::class, $build);
        static::assertEquals('Foo!', $build->pintoGet('text'));
        // '3' comes from the entrypoint (constructor).
        static::assertEquals(3, $build->pintoGet('number'));
    }

    public function testDefinitionsSlotsAttrOnObject(): void
    {
        $themeDefinitions = PintoTestUtility::definitions(PintoListSlots::class, new \Pinto\DefinitionDiscovery());
        static::assertCount(9, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[PintoListSlots::Slots];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'text', origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsBasic::class, '__construct'], 'text'))),
            new Slots\Slot(name: 'number', defaultValue: 3, origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsBasic::class, '__construct'], 'number'))),
        ]), $slotsDefinition->slots);

        $slotsDefinition = $themeDefinitions[PintoListSlots::SlotsAttributeOnMethod];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'foo', defaultValue: null, origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsAttributeOnMethod::class, 'create'], 'foo'))),
            new Slots\Slot(name: 'arr', defaultValue: [], origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsAttributeOnMethod::class, 'create'], 'arr'))),
        ]), $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrOnList(): void
    {
        $themeDefinitions = PintoTestUtility::definitions(Lists\PintoListSlotsOnEnum::class, new \Pinto\DefinitionDiscovery());
        static::assertCount(1, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsOnEnum::SlotsOnEnum];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'fooFromList', origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsFromList::class, '__construct'], 'fooFromList'))),
            new Slots\Slot(name: 'number', defaultValue: 4, origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsFromList::class, '__construct'], 'number'))),
        ]), $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrOnListCase(): void
    {
        $themeDefinitions = PintoTestUtility::definitions(Lists\PintoListSlotsOnEnumCase::class, new \Pinto\DefinitionDiscovery());
        static::assertCount(1, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsOnEnumCase::SlotsOnEnumCase];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'fooFromListCase', origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsFromListCase::class, '__construct'], 'fooFromListCase'))),
        ]), $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrByInheritance(): void
    {
        $definitionDiscovery = new \Pinto\DefinitionDiscovery();
        $definitionDiscovery[PintoObjectSlotsByInheritanceChild::class] = Lists\PintoListSlotsByInheritance::SlotsByInheritanceChild;
        $definitionDiscovery[PintoObjectSlotsByInheritanceGrandParent::class] = Lists\PintoListSlotsByInheritance::SlotsByInheritanceGrandParent;
        $themeDefinitions = PintoTestUtility::definitions(Lists\PintoListSlotsByInheritance::class, $definitionDiscovery);
        static::assertCount(3, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsByInheritance::SlotsByInheritanceChild];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'fooFromGrandParent', origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsByInheritanceGrandParent::class, '__construct'], 'fooFromGrandParent'))),
        ]), $slotsDefinition->slots);
    }

    /**
     * @covers \Pinto\Slots\Attribute\ModifySlots::__construct
     */
    public function testModifySlotsAttributeNamedParameters(): void
    {
        static::expectException(\LogicException::class);
        static::expectExceptionMessage('Using this attribute without named parameters is not supported.');
        new Slots\Attribute\ModifySlots('');
    }

    /**
     * @covers \Pinto\Slots\Attribute\ModifySlots::__construct
     */
    public function testModifySlotsAttributeAddMissingSlots(): void
    {
        static::expectException(\LogicException::class);
        static::expectExceptionMessage('Slots must be added.');
        new Slots\Attribute\ModifySlots(add: []);
    }

    public function testDefinitionsSlotsAttrByInheritanceModifiedSlots(): void
    {
        $definitionDiscovery = new \Pinto\DefinitionDiscovery();
        $definitionDiscovery[PintoObjectSlotsByInheritanceChild::class] = Lists\PintoListSlotsByInheritance::SlotsByInheritanceChild;
        $definitionDiscovery[PintoObjectSlotsByInheritanceChildModifySlots::class] = Lists\PintoListSlotsByInheritance::PintoObjectSlotsByInheritanceChildModifySlots;
        $definitionDiscovery[PintoObjectSlotsByInheritanceGrandParent::class] = Lists\PintoListSlotsByInheritance::SlotsByInheritanceGrandParent;
        $themeDefinitions = PintoTestUtility::definitions(Lists\PintoListSlotsByInheritance::class, $definitionDiscovery);
        static::assertCount(3, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsByInheritance::PintoObjectSlotsByInheritanceChildModifySlots];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'fooFromGrandParent', origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsByInheritanceGrandParent::class, '__construct'], 'fooFromGrandParent'))),
            new Slots\Slot(name: 'new_slot', origin: new Slots\Origin\StaticallyDefined()),
        ]), $slotsDefinition->slots);
    }

    public function testDefinitionsSlotsAttrByInheritanceGrandParentUnregistered(): void
    {
        // It the parent isn't registered to an enum, no object type is determined.
        $definitionDiscovery = new \Pinto\DefinitionDiscovery();
        // Normally parent is set here.
        $definitionDiscovery[PintoObjectSlotsByInheritanceChild::class] = Lists\PintoListSlotsByInheritance::SlotsByInheritanceChild;

        static::expectException(\Pinto\Exception\PintoIndeterminableObjectType::class);
        PintoTestUtility::definitions(Lists\PintoListSlotsByInheritance::class, $definitionDiscovery);
    }

    public function testDefinitionsSlotsAttrOnListMethodSpecified(): void
    {
        $themeDefinitions = PintoTestUtility::definitions(Lists\PintoListSlotsOnEnumMethodSpecified::class, new \Pinto\DefinitionDiscovery());
        static::assertCount(1, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsOnEnumMethodSpecified::SlotsOnEnumMethodSpecified];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'create', defaultValue: 'from method specified on enum #[Slots]', origin: Slots\Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsFromListMethodSpecified::class, 'create'], 'create'))),
        ]), $slotsDefinition->slots);
    }

    public function testSlotAttribute(): void
    {
        $attr = new \Pinto\Attribute\ObjectType\Slots(slots: [
            new Slots\Slot(name: 'foo'),
            'bar',
        ]);

        static::assertEquals([
            new Slots\Slot(name: 'foo', origin: Slots\Origin\StaticallyDefined::create(data: null)),
            new Slots\Slot(name: 'bar', origin: Slots\Origin\StaticallyDefined::create(data: 'bar')),
        ], $attr->slots->toArray());
    }

    public function testSlotNamedParameters(): void
    {
        static::expectException(\LogicException::class);
        static::expectExceptionMessage('Using this attribute without named parameters is not supported.');
        new Slots\Slot('slotname', origin: Slots\Origin\StaticallyDefined::create(data: 'slotname'), useNamedParameters: 'defaultvalue');
    }

    public function testSlotAttributeNamedParameters(): void
    {
        static::expectException(\LogicException::class);
        static::expectExceptionMessage('Using this attribute without named parameters is not supported.');
        new \Pinto\Attribute\ObjectType\Slots('');
    }

    /**
     * @covers \Pinto\Slots\Attribute\RenameSlot
     * @covers \Pinto\Slots\RenameSlots
     */
    public function testRenameSlots(): void
    {
        $definitionDiscovery = new \Pinto\DefinitionDiscovery();
        $definitionDiscovery[PintoObjectSlotsRenameParent::class] = Lists\PintoListSlotsRename::SlotsRenameParent;
        $definitionDiscovery[PintoObjectSlotsRenameChild::class] = Lists\PintoListSlotsRename::SlotsRenameChild;
        $themeDefinitions = PintoTestUtility::definitions(Lists\PintoListSlotsRename::class, $definitionDiscovery);
        static::assertCount(2, $themeDefinitions);

        $slotsDefinition = $themeDefinitions[Lists\PintoListSlotsRename::SlotsRenameChild];
        static::assertInstanceOf(Slots\Definition::class, $slotsDefinition);
        static::assertEquals(new SlotList([
            new Slots\Slot(name: 'slotFromParentUnrenamed', origin: Slots\Origin\StaticallyDefined::create(data: 'slotFromParentUnrenamed')),
            new Slots\Slot(name: 'stringFromParentThatWillBeRenamed', origin: Slots\Origin\StaticallyDefined::create(data: 'stringFromParentThatWillBeRenamed')),
            new Slots\Slot(name: SlotEnum::Slot1, origin: Slots\Origin\EnumCase::createFromEnum(SlotEnum::Slot1)),
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
