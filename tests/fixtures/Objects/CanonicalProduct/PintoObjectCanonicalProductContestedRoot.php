<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\CanonicalProduct;

use Pinto\Attribute\ObjectType;
use Pinto\CanonicalProduct\Attribute\CanonicalProduct;
use Pinto\CanonicalProduct\CanonicalFactoryTrait;
use Pinto\DefinitionDiscovery;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\tests\fixtures\Lists\CanonicalProduct\PintoListCanonicalProductContested;

/**
 * CanonicalProduct test object.
 */
#[ObjectType\Slots]
class PintoObjectCanonicalProductContestedRoot
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
        $definitionDiscovery[PintoObjectCanonicalProductContestedChild1::class] = PintoListCanonicalProductContested::Child1;
        $definitionDiscovery[PintoObjectCanonicalProductContestedChild2::class] = PintoListCanonicalProductContested::Child2;
        $definitionDiscovery[PintoObjectCanonicalProductContestedRoot::class] = PintoListCanonicalProductContested::Root;

        return new PintoMapping(
            enumClasses: [
                // Not tested.
            ],
            enums: [
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
