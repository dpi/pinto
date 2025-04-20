<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists\CanonicalProduct;

use Pinto\Attribute\Definition;
use Pinto\Attribute\ObjectType;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductHierarchyChild;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductHierarchyChild2;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductHierarchyGrandParent;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductHierarchyGreatGrandParent;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductHierarchyParent;

#[ObjectType\Slots]
enum PintoListCanonicalProductHierarchy implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObjectCanonicalProductHierarchyGreatGrandParent::class)]
    case GreatGrandParent;

    #[Definition(PintoObjectCanonicalProductHierarchyGrandParent::class)]
    case GrandParent;

    #[Definition(PintoObjectCanonicalProductHierarchyParent::class)]
    case Parent;

    #[Definition(PintoObjectCanonicalProductHierarchyChild::class)]
    case Child;

    #[Definition(PintoObjectCanonicalProductHierarchyChild2::class)]
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
