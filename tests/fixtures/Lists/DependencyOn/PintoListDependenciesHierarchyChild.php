<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists\DependencyOn;

use Pinto\Attribute\Definition;
use Pinto\Attribute\DependencyOn;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\DependencyOn\PintoObjectDependencyOnChild;

use function Safe\realpath;

#[DependencyOn(parent: true)]
enum PintoListDependenciesHierarchyChild implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObjectDependencyOnChild::class)]
    case Child;

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
