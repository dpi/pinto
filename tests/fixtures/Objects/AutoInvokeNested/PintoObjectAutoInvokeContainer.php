<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\AutoInvokeNested;

use Pinto\Attribute\ObjectType;
use Pinto\DefinitionDiscovery;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\tests\fixtures\Lists\AutoInvokeNested\PintoListAutoInvokeNested;

/**
 * PintoListAutoInvokeNested test object.
 */
#[ObjectType\Slots(slots: [
    'text',
    'child_1',
    'child_2',
])]
class PintoObjectAutoInvokeContainer
{
    use ObjectTrait;

    private PintoObjectAutoInvokeChild1 $child1;

    final public function __construct(
        private string $foo,
    ) {
        $this->child1 = new PintoObjectAutoInvokeChild1();
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Slots\Build $build): Slots\Build {
            return $build
              ->set('text', $this->foo)
              ->set('child_1', $this->child1)
              ->set('child_2', new PintoObjectAutoInvokeChild2())
            ;
        });
    }

    public static function pintoMappingStatic(): PintoMapping
    {
        $definitionDiscovery = new DefinitionDiscovery();
        $definitionDiscovery[PintoObjectAutoInvokeContainer::class] = PintoListAutoInvokeNested::Containing;
        $definitionDiscovery[PintoObjectAutoInvokeChild1::class] = PintoListAutoInvokeNested::Child1;
        $definitionDiscovery[PintoObjectAutoInvokeChild2::class] = PintoListAutoInvokeNested::Child2;
        $definitionDiscovery[PintoObjectAutoInvokeChild3::class] = PintoListAutoInvokeNested::Child3;

        return new PintoMapping(
            enumClasses: [
                // Not tested.
            ],
            enums: [
                static::class => [PintoListAutoInvokeNested::class, PintoListAutoInvokeNested::Containing->name],
                PintoObjectAutoInvokeChild1::class => [PintoListAutoInvokeNested::class, PintoListAutoInvokeNested::Child1->name],
                PintoObjectAutoInvokeChild2::class => [PintoListAutoInvokeNested::class, PintoListAutoInvokeNested::Child2->name],
                PintoObjectAutoInvokeChild3::class => [PintoListAutoInvokeNested::class, PintoListAutoInvokeNested::Child3->name],
            ],
            definitions: [
                static::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'text'),
                    new Slots\Slot(name: 'child_1'),
                    new Slots\Slot(name: 'child_2'),
                ])),
                PintoObjectAutoInvokeChild1::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'child1_text'),
                ])),
                PintoObjectAutoInvokeChild2::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'child2_text'),
                    new Slots\Slot(name: 'child2_child'),
                ])),
                PintoObjectAutoInvokeChild3::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'child3_text'),
                ])),
            ],
            buildInvokers: [
                static::class => '__invoke',
                PintoObjectAutoInvokeChild1::class => '__invoke',
                PintoObjectAutoInvokeChild2::class => '__invoke',
                PintoObjectAutoInvokeChild3::class => '__invoke',
            ],
            types: [
                static::class => ObjectType\Slots::class,
                PintoObjectAutoInvokeChild1::class => ObjectType\Slots::class,
                PintoObjectAutoInvokeChild2::class => ObjectType\Slots::class,
                PintoObjectAutoInvokeChild3::class => ObjectType\Slots::class,
            ],
            lsbFactoryCanonicalObjectClasses: [],
        );
    }

    private function pintoMapping(): PintoMapping
    {
        return self::pintoMappingStatic();
    }
}
