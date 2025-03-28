<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Definition;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsRenameChild;
use Pinto\tests\fixtures\Objects\Slots\PintoObjectSlotsRenameParent;

enum PintoListSlotsRename implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObjectSlotsRenameParent::class)]
    case SlotsRenameParent;

    #[Definition(PintoObjectSlotsRenameChild::class)]
    case SlotsRenameChild;

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
