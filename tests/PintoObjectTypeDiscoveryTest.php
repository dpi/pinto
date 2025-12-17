<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Attribute\ObjectType;
use Pinto\Exception\PintoIndeterminableObjectType;
use Pinto\Exception\PintoObjectTypeDefinition;
use Pinto\List\Resource\ObjectListEnumResource;
use Pinto\ObjectType\ObjectTypeDiscovery;
use Pinto\ObjectType\ObjectTypeInterface;
use Pinto\Resource\ResourceInterface;
use Pinto\Slots;
use Pinto\tests\fixtures\Objects\Faulty\PintoObjectZeroObjectTypeAttributes;

/**
 * @coversDefaultClass \Pinto\ObjectType\ObjectTypeDiscovery
 */
final class PintoObjectTypeDiscoveryTest extends TestCase
{
    public function testZeroObjectTypeAttributes(): void
    {
        static::expectException(PintoIndeterminableObjectType::class);
        static::expectExceptionMessage(sprintf('Missing %s attribute on %s or a parent class or %s or %s::%s',
            ObjectTypeInterface::class,
            PintoObjectZeroObjectTypeAttributes::class,
            fixtures\Lists\PintoFaultyList::class,
            fixtures\Lists\PintoFaultyList::class,
            fixtures\Lists\PintoFaultyList::PintoObjectZeroObjectTypeAttributes->name,
        ));
        ObjectTypeDiscovery::definitionForThemeObject(PintoObjectZeroObjectTypeAttributes::class, ObjectListEnumResource::createFromEnum(fixtures\Lists\PintoFaultyList::PintoObjectZeroObjectTypeAttributes), definitionDiscovery: new \Pinto\DefinitionDiscovery());
    }

    public function testMultipleObjectTypeAttributes(): void
    {
        static::expectException(PintoObjectTypeDefinition::class);
        static::expectExceptionMessage(sprintf('Multiple theme definitions found on %s. There must only be one.', fixtures\Objects\Faulty\PintoObjectMultipleObjectTypeAttributes::class));
        ObjectTypeDiscovery::definitionForThemeObject(fixtures\Objects\Faulty\PintoObjectMultipleObjectTypeAttributes::class, fixtures\Lists\PintoFaultyList::PintoObjectMultipleObjectTypeAttributes, definitionDiscovery: new \Pinto\DefinitionDiscovery());
    }

    public function testDefinitionForThemeObject(): void
    {
        $definition = ObjectTypeDiscovery::definitionForThemeObject(fixtures\Objects\PintoObject::class, fixtures\Lists\PintoList::Pinto_Object, definitionDiscovery: new \Pinto\DefinitionDiscovery())[1];
        static::assertInstanceOf(Slots\Definition::class, $definition);
        static::assertEquals(new Slots\Definition(
            slots: new Slots\SlotList([
                new Slots\Slot(name: 'test_variable', origin: Slots\Origin\StaticallyDefined::create(data: 'test_variable')),
            ]),
            renameSlots: Slots\RenameSlots::create(),
        ), $definition);
    }

    /**
     * Ensures objects registered always have an ObjectType attribute if they are not an enum resource.
     */
    public function testResourceNotEnumWhenNoObjectTypeFound(): void
    {
        static::expectException(PintoObjectTypeDefinition::class);
        static::expectExceptionMessage(sprintf('Resource for %s is not a %s', PintoObjectZeroObjectTypeAttributes::class, ObjectListEnumResource::class));

        $resource = $this->createMock(ResourceInterface::class);
        ObjectTypeDiscovery::definitionForThemeObject(PintoObjectZeroObjectTypeAttributes::class, $resource, definitionDiscovery: new \Pinto\DefinitionDiscovery());
    }
}
