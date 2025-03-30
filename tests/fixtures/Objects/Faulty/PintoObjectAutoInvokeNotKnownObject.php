<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Faulty;

use Pinto\Attribute\ObjectType;
use Pinto\DefinitionDiscovery;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\tests\fixtures\Lists\PintoFaultyList;

/**
 * Where a slot is filled by an object which is not known.
 *
 * Coverage for \Pinto\ObjectType\LateBindObjectContext::getBuildInvoker null return.
 */
#[ObjectType\Slots(slots: [
    'child',
])]
class PintoObjectAutoInvokeNotKnownObject
{
    use ObjectTrait;

    final public function __construct()
    {
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Slots\Build $build): Slots\Build {
            return $build
              ->set('child', new \stdClass());
        });
    }

    public static function pintoMappingStatic(): PintoMapping
    {
        $definitionDiscovery = new DefinitionDiscovery();
        $definitionDiscovery[static::class] = PintoFaultyList::PintoObjectAutoInvokeNotKnownObject;

        return new PintoMapping(
            enumClasses: [
                // Not tested.
            ],
            enums: [
                static::class => [PintoFaultyList::class, PintoFaultyList::PintoObjectAutoInvokeNotKnownObject->name],
            ],
            definitions: [
                static::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'child'),
                ])),
            ],
            buildInvokers: [
                static::class => '__invoke',
            ],
            types: [
                static::class => ObjectType\Slots::class,
            ],
            lsbFactoryCanonicalObjectClasses: [],
        );
    }

    private function pintoMapping(): PintoMapping
    {
        return self::pintoMappingStatic();
    }
}
