<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Definition;
use Pinto\Attribute\ObjectType;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsFromListMethodSpecified;

#[ObjectType\Slots(method: 'create')]
enum PintoListSlotsOnEnumMethodSpecified implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObjectSlotsFromListMethodSpecified::class)]
    case SlotsOnEnumMethodSpecified;

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
