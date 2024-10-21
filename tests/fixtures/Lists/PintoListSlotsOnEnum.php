<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Definition;
use Pinto\Attribute\ObjectType;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsFromList;

#[ObjectType\Slots]
enum PintoListSlotsOnEnum implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObjectSlotsFromList::class)]
    case SlotsOnEnum;

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
