<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists\DependencyOn;

use Pinto\Attribute\Definition;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\DependencyOn\PintoObjectDependencyOnParent;

use function Safe\realpath;

enum PintoListDependenciesHierarchyParent implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObjectDependencyOnParent::class)]
    case Parent;

    public function templateDirectory(): string
    {
        return 'tests/fixtures/resources';
    }

    public function cssDirectory(): string
    {
        return realpath(__DIR__ . '/../../resources/css');
    }

    public function jsDirectory(): string
    {
        return realpath(__DIR__ . '/../../resources/javascript');
    }
}
