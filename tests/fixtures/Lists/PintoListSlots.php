<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Definition;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\Slots;

enum PintoListSlots implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(Slots\PintoObjectSlotsBasic::class)]
    case Slots;

    #[Definition(Slots\PintoObjectSlotsAttributeOnMethod::class)]
    case SlotsAttributeOnMethod;

    #[Definition(Slots\PintoObjectSlotsMissingSlotValue::class)]
    case SlotMissingValue;

    #[Definition(Slots\PintoObjectSlotsMissingSlotValueWithDefault::class)]
    case PintoObjectSlotsMissingSlotValueWithDefault;

    #[Definition(Slots\PintoObjectSlotsExplicitEnumClass::class)]
    case PintoObjectSlotsExplicitEnumClass;

    #[Definition(Slots\PintoObjectSlotsBindPromotedPublic::class)]
    case PintoObjectSlotsBindPromotedPublic;

    public function templateDirectory(): string
    {
        return 'tests/fixtures/resources';
    }

    public function cssDirectory(): string
    {
        return 'tests/fixtures/resources';
    }

    public function jsDirectory(): string
    {
        return 'tests/fixtures/resources';
    }
}
