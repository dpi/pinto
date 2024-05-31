<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Definition;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\PintoObject;
use Pinto\tests\fixtures\Objects\PintoObjectAttributes;

enum PintoList: string implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObject::class)]
    case Pinto_Object = 'object_test';

    #[Definition(PintoObjectAttributes::class)]
    case Pinto_Object_Attributes = 'object_test_attributes';

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
