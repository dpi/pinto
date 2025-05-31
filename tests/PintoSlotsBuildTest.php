<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pinto\Exception\Slots\UnknownValue;
use Pinto\Slots;
use Pinto\Slots\SlotList;
use Pinto\tests\fixtures\Etc\SlotEnum;

#[PHPUnit\Framework\Attributes\CoversClass(Slots\Build::class)]
final class PintoSlotsBuildTest extends TestCase
{
    /**
     * @covers ::pintoGet
     */
    public function testPintoGetStringSlotDoesntExist(): void
    {
        $build = Slots\Build::create(new SlotList());
        static::expectException(UnknownValue::class);
        static::expectExceptionMessage('Unknown slot `foo`');
        $build->pintoGet('foo');
    }

    /**
     * @covers ::pintoGet
     */
    public function testPintoGetEnumSlotDoesntExist(): void
    {
        $build = Slots\Build::create(new SlotList());
        static::expectException(UnknownValue::class);
        static::expectExceptionMessage('Unknown slot `' . SlotEnum::class . '::Slot1`');
        $build->pintoGet(SlotEnum::Slot1);
    }

    /**
     * @covers ::pintoGet
     */
    public function testPintoGetStringSlotNoValue(): void
    {
        $build = Slots\Build::create(new SlotList([new Slots\Slot(name: 'foo')]));
        static::expectException(UnknownValue::class);
        static::expectExceptionMessage('Value not set for slot `foo`');
        $build->pintoGet('foo');
    }

    /**
     * @covers ::pintoGet
     */
    public function testPintoGetEnumSlotNoValue(): void
    {
        $build = Slots\Build::create(new SlotList([new Slots\Slot(name: SlotEnum::Slot1)]));
        static::expectException(UnknownValue::class);
        static::expectExceptionMessage('Value not set for slot `' . SlotEnum::class . '::Slot1`');
        $build->pintoGet(SlotEnum::Slot1);
    }

    /**
     * @covers ::pintoHas
     */
    public function testPintoHasEnumSlot(): void
    {
        $slotList = new SlotList([
            new Slots\Slot(name: SlotEnum::Slot1),
        ]);
        $build = Slots\Build::create($slotList);
        static::assertFalse($build->pintoHas(SlotEnum::Slot1));

        // Emptyish value fills slot.
        $build = Slots\Build::create($slotList)->set(SlotEnum::Slot1, null);
        static::assertTrue($build->pintoHas(SlotEnum::Slot1));

        // Non emptyish value fills slot.
        $build = Slots\Build::create($slotList)->set(SlotEnum::Slot1, 'foo');
        static::assertTrue($build->pintoHas(SlotEnum::Slot1));
    }
}
