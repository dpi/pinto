<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Exception\PintoIndeterminableObjectType;
use Pinto\Exception\PintoThemeDefinition;
use Pinto\List\Resource\ObjectListEnumResource;
use Pinto\ObjectType\ObjectTypeDiscovery;
use Pinto\ObjectType\ObjectTypeInterface;
use Pinto\Resource\ResourceInterface;
use Pinto\tests\fixtures\Objects\Faulty\PintoObjectZeroObjectTypeAttributes;
use Pinto\ThemeDefinition\HookThemeDefinition;

use function Safe\realpath;

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
        static::expectException(PintoThemeDefinition::class);
        static::expectExceptionMessage(sprintf('Multiple theme definitions found on %s. There must only be one.', fixtures\Objects\Faulty\PintoObjectMultipleObjectTypeAttributes::class));
        ObjectTypeDiscovery::definitionForThemeObject(fixtures\Objects\Faulty\PintoObjectMultipleObjectTypeAttributes::class, fixtures\Lists\PintoFaultyList::PintoObjectMultipleObjectTypeAttributes, definitionDiscovery: new \Pinto\DefinitionDiscovery());
    }

    public function testDefinitionForThemeObject(): void
    {
        $definition = ObjectTypeDiscovery::definitionForThemeObject(fixtures\Objects\PintoObject::class, fixtures\Lists\PintoList::Pinto_Object, definitionDiscovery: new \Pinto\DefinitionDiscovery())[1];
        static::assertInstanceOf(HookThemeDefinition::class, $definition);
        static::assertEquals(
            [
                'variables' => [
                    'test_variable' => null,
                ],
                'path' => realpath(__DIR__ . '/fixtures/resources'),
                'template' => 'object-test',
            ],
            $definition->definition,
        );
    }

    /**
     * Ensures objects registered always have an ObjectType attribute if they are not an enum resource.
     */
    public function testResourceNotEnumWhenNoObjectTypeFound(): void
    {
        static::expectException(PintoThemeDefinition::class);
        static::expectExceptionMessage(sprintf('Resource for %s is not a %s', PintoObjectZeroObjectTypeAttributes::class, ObjectListEnumResource::class));

        $resource = $this->createMock(ResourceInterface::class);
        ObjectTypeDiscovery::definitionForThemeObject(PintoObjectZeroObjectTypeAttributes::class, $resource, definitionDiscovery: new \Pinto\DefinitionDiscovery());
    }
}
