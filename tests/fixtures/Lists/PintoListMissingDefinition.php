<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;

enum PintoListMissingDefinition: string implements ObjectListInterface
{
    use ObjectListTrait;

    case Pinto_Missing_Enum = 'missing_object_test';

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
