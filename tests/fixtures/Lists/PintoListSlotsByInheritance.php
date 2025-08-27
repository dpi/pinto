<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Definition;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsByInheritanceChild;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsByInheritanceChildModifySlots;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsByInheritanceGrandParent;

enum PintoListSlotsByInheritance implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObjectSlotsByInheritanceGrandParent::class)]
    case SlotsByInheritanceGrandParent;

    #[Definition(PintoObjectSlotsByInheritanceChild::class)]
    case SlotsByInheritanceChild;

    #[Definition(PintoObjectSlotsByInheritanceChildModifySlots::class)]
    case PintoObjectSlotsByInheritanceChildModifySlots;

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
