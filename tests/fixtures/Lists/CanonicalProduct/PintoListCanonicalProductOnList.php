<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists\CanonicalProduct;

use Pinto\Attribute\Definition;
use Pinto\Attribute\ObjectType;
use Pinto\CanonicalProduct\Attribute\CanonicalProduct;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductOnListChild1;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductOnListChild2;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductOnListRoot1;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductOnListRoot2;

#[ObjectType\Slots]
#[CanonicalProduct]
enum PintoListCanonicalProductOnList implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObjectCanonicalProductOnListRoot1::class)]
    case Root1;

    #[Definition(PintoObjectCanonicalProductOnListRoot2::class)]
    case Root2;

    #[Definition(PintoObjectCanonicalProductOnListChild1::class)]
    case Child1;

    #[Definition(PintoObjectCanonicalProductOnListChild2::class)]
    case Child2;

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
