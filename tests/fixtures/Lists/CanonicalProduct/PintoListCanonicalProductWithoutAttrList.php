<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists\CanonicalProduct;

use Pinto\Attribute\Definition;
use Pinto\Attribute\ObjectType;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductWithoutAttrChild;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductWithoutAttrRoot;

#[ObjectType\Slots]
enum PintoListCanonicalProductWithoutAttrList implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObjectCanonicalProductWithoutAttrRoot::class)]
    case Root;

    #[Definition(PintoObjectCanonicalProductWithoutAttrChild::class)]
    case Child;

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
