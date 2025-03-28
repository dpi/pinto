<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Exception\PintoIndeterminableObjectType;
use Pinto\Exception\PintoThemeDefinition;
use Pinto\ObjectType\ObjectTypeDiscovery;
use Pinto\ObjectType\ObjectTypeInterface;
use Pinto\ThemeDefinition\HookThemeDefinition;

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
            fixtures\Objects\Faulty\PintoObjectZeroObjectTypeAttributes::class,
            fixtures\Lists\PintoFaultyList::class,
            fixtures\Lists\PintoFaultyList::class,
            fixtures\Lists\PintoFaultyList::PintoObjectZeroObjectTypeAttributes->name,
        ));
        ObjectTypeDiscovery::definitionForThemeObject(fixtures\Objects\Faulty\PintoObjectZeroObjectTypeAttributes::class, fixtures\Lists\PintoFaultyList::PintoObjectZeroObjectTypeAttributes, definitionDiscovery: new \Pinto\DefinitionDiscovery());
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
                'path' => 'tests/fixtures/resources',
                'template' => 'object-test',
            ],
            $definition->definition,
        );
    }
}
