<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\CanonicalProduct;

use Pinto\Attribute\ObjectType;
use Pinto\CanonicalProduct\Attribute\CanonicalProduct;
use Pinto\CanonicalProduct\CanonicalFactoryTrait;
use Pinto\DefinitionDiscovery;
use Pinto\List\Resource\ObjectListEnumResource;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\tests\fixtures\Lists\CanonicalProduct\PintoListCanonicalProductOnList;

/**
 * CanonicalProduct test object.
 */
#[ObjectType\Slots]
class PintoObjectCanonicalProductOnListRoot2
{
    use ObjectTrait;

    use CanonicalFactoryTrait;

    final public function __construct()
    {
    }

    public function __invoke(): mixed
    {
        throw new \LogicException('Object level logic not tested.');
    }

    private static function pintoMappingStatic(): PintoMapping
    {
        $definitionDiscovery = new DefinitionDiscovery();
        $definitionDiscovery[PintoObjectCanonicalProductOnListChild2::class] = ObjectListEnumResource::createFromEnum(PintoListCanonicalProductOnList::Child2);
        $definitionDiscovery[PintoObjectCanonicalProductOnListRoot2::class] = ObjectListEnumResource::createFromEnum(PintoListCanonicalProductOnList::Root2);

        return new PintoMapping(
            resources: [
                // Not tested.
            ],
            definitions: [
                // Not tested.
            ],
            buildInvokers: [
                static::class => '__invoke',
            ],
            types: [static::class => ObjectType\Slots::class],
            lsbFactoryCanonicalObjectClasses: CanonicalProduct::discoverCanonicalProductObjectClasses($definitionDiscovery),
        );
    }

    private function pintoMapping(): PintoMapping
    {
        return self::pintoMappingStatic();
    }
}
