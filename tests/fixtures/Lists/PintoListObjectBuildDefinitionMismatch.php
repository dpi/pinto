<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Definition;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\PintoObjectBuildDefinitionMismatch;

enum PintoListObjectBuildDefinitionMismatch: string implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObjectBuildDefinitionMismatch::class)]
    case Pinto_Object_Build_Definition_Mismatch = 'object_test_build_def_mismatch';

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
