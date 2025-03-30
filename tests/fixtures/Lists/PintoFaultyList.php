<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Definition;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures;

/**
 * A list of faulty objects. The list is not in itself faulty.
 */
enum PintoFaultyList implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(fixtures\Objects\Faulty\PintoObjectZeroObjectTypeAttributes::class)]
    case PintoObjectZeroObjectTypeAttributes;

    #[Definition(fixtures\Objects\Faulty\PintoObjectMultipleObjectTypeAttributes::class)]
    case PintoObjectMultipleObjectTypeAttributes;

    #[Definition(fixtures\Objects\Faulty\PintoObjectSlotsBindPromotedPublicWithDefinedSlots::class)]
    case PintoObjectSlotsBindPromotedPublicWithDefinedSlots;

    #[Definition(fixtures\Objects\Faulty\PintoObjectAutoInvokeNotKnownObject::class)]
    case PintoObjectAutoInvokeNotKnownObject;

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
