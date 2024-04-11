<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Definition;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\tests\fixtures\Objects\PintoObject;

enum PintoBuildOverrideList: string implements ObjectListInterface
{
    use ObjectListTrait;

    #[Definition(PintoObject::class)]
    case PintoBuildOverrideObject = 'object_test';

    public function build(callable $wrapper, object $object): callable
    {
        return function (mixed $build) use ($wrapper, $object) {
            if (is_array($build)) {
                $build['build_context_from_list'] = $object::class;
            }

            return $wrapper($build);
        };
    }

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
