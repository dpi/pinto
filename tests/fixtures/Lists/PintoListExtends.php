<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Definition;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\Extends\PintoObjectExtends1;
use Pinto\tests\fixtures\Objects\Extends\PintoObjectExtends2;

enum PintoListExtends: string implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObjectExtends1::class)]
    case Extends1 = 'extends1';

    #[Definition(PintoObjectExtends2::class)]
    case Extends2 = 'extends2';

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
