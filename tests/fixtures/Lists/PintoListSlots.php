<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Definition;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsAttributeOnMethod;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsBasic;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsMissingSlotValue;

enum PintoListSlots implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObjectSlotsBasic::class)]
    case Slots;

    #[Definition(PintoObjectSlotsAttributeOnMethod::class)]
    case SlotsAttributeOnMethod;

    #[Definition(PintoObjectSlotsMissingSlotValue::class)]
    case SlotMissingValue;

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
