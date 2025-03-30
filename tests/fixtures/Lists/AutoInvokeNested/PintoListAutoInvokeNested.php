<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists\AutoInvokeNested;

use Pinto\Attribute\Definition;
use Pinto\Attribute\ObjectType;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductChild;
use Pinto\tests\fixtures\Objects\CanonicalProduct\PintoObjectCanonicalProductRoot;

#[ObjectType\Slots]
enum PintoListAutoInvokeNested implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObjectCanonicalProductRoot::class)]
    case Containing;

    #[Definition(PintoObjectCanonicalProductChild::class)]
    case Child1;

    #[Definition(PintoObjectCanonicalProductChild::class)]
    case Child2;

    case Child3;

    public function templateDirectory(): string
    {
        throw new \LogicException('Object level logic not tested.');
    }

    public function cssDirectory(): string
    {
        throw new \LogicException('Object level logic not tested.');
    }

    public function jsDirectory(): string
    {
        throw new \LogicException('Object level logic not tested.');
    }
}
