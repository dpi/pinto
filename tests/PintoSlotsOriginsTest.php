<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Slots\Origin;
use Pinto\tests\fixtures\Etc\SlotEnum;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsBasic;

final class PintoSlotsOriginsTest extends TestCase
{
    /**
     * @see Origin\EnumCase
     */
    public function testEnumCase(): void
    {
        $origin = Origin\EnumCase::createFromEnum(SlotEnum::Slot1);
        static::assertEquals(SlotEnum::Slot1, $origin->enumCase());
    }

    /**
     * Test a serialised origin, but since gone/renamed, etc.
     */
    public function testEnumCaseNonExistent(): void
    {
        $constructor = (new \ReflectionClass(Origin\EnumCase::class))->getConstructor() ?? throw new \LogicException('impossible');
        $origin = (new \ReflectionClass(Origin\EnumCase::class))->newInstanceWithoutConstructor();
        $constructor->setAccessible(true);
        $constructor->invokeArgs($origin, ['class name', 'case name']);

        static::expectException(\InvalidArgumentException::class);
        static::expectExceptionMessage('class name::case name does not exist');
        $origin->enumCase();
    }

    /**
     * @see Origin\StaticallyDefined
     */
    public function testStaticallyDefined(): void
    {
        $origin = Origin\StaticallyDefined::create('foo');
        static::assertEquals('foo', $origin->data());
    }

    /**
     * @see Origin\StaticallyDefined
     */
    public function testParameterReflection(): void
    {
        $origin = Origin\Parameter::fromReflection(new \ReflectionParameter([PintoObjectSlotsBasic::class, '__construct'], 'text'));
        static::assertEquals('text', $origin->parameterReflection()->getName());
    }

    /**
     * Test a serialised origin, but since gone/renamed, etc.
     */
    public function testParameterReflectionNonExistent(): void
    {
        $constructor = (new \ReflectionClass(Origin\Parameter::class))->getConstructor() ?? throw new \LogicException('impossible');
        $origin = (new \ReflectionClass(Origin\Parameter::class))->newInstanceWithoutConstructor();
        $constructor->setAccessible(true);
        $constructor->invokeArgs($origin, ['parameter name', '__construct', PintoObjectSlotsBasic::class]);

        static::expectException(\InvalidArgumentException::class);
        $origin->parameterReflection();
    }
}
