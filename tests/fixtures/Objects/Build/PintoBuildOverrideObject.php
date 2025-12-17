<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Build;

use Pinto\Attribute\ObjectType;
use Pinto\List\Resource\ObjectListEnumResource;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\tests\fixtures\Lists\PintoBuildOverrideList;

/**
 * Test where the list enum overrides the build of this object.
 *
 * @see PintoBuildOverrideList::build
 */
final class PintoBuildOverrideObject
{
    use ObjectTrait;

    /**
     * Constructor.
     */
    private function __construct(
        public readonly string $text,
    ) {
    }

    #[ObjectType\Slots(slots: ['test_variable', 'build_context_from_list'])]
    public static function create(
        string $text,
    ): static {
        return new static($text);
    }

    public function __invoke(): mixed
    {
        return $this->pintoBuild(function (Slots\Build $build): Slots\Build {
            return $build
              ->set('test_variable', $this->text)
            ;
        });
    }

    private function pintoMapping(): PintoMapping
    {
        return new PintoMapping(
            resources: [
                static::class => ObjectListEnumResource::createFromEnum(PintoBuildOverrideList::PintoBuildOverrideObject),
            ],
            definitions: [
                static::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'test_variable'),
                    new Slots\Slot(name: 'build_context_from_list'),
                ])),
            ],
            buildInvokers: [
                static::class => '__invoke',
            ],
            types: [static::class => ObjectType\Slots::class],
            lsbFactoryCanonicalObjectClasses: [],
        );
    }
}
