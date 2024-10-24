<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Definition;
use Pinto\Attribute\ObjectType;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;
use Pinto\List\SingleDirectoryObjectListTrait;
use Pinto\tests\fixtures\Objects\PintoObject;

#[ObjectType\Slots]
enum PintoSingleDirectoryObjectList implements ObjectListInterface
{
    use ObjectListTrait;
    use SingleDirectoryObjectListTrait;

    #[Definition(PintoObject::class)]
    case SDO;

    public function directory(): string
    {
        return '/a/directory/with/shared/resources';
    }
}
