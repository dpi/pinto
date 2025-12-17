<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects;

use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\ExternalCss;
use Pinto\Attribute\Asset\ExternalJs;
use Pinto\Attribute\Asset\Js;
use Pinto\Attribute\ObjectType;
use Pinto\List\Resource\ObjectListEnumResource;
use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;
use Pinto\tests\fixtures\Lists\PintoList;

/**
 * Test object.
 */
#[Css('styles.css')]
#[Js('app.js')]
#[ExternalJs('https://example.com/path.js')]
#[ExternalCss('https://example.com/path.css')]
final class PintoObject
{
    use ObjectTrait;

    /**
     * Constructor.
     */
    private function __construct(
        public readonly string $text,
    ) {
    }

    #[ObjectType\Slots(slots: ['test_variable'])]
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
                static::class => ObjectListEnumResource::createFromEnum(PintoList::Pinto_Object),
            ],
            definitions: [
                static::class => new Slots\Definition(new Slots\SlotList([
                    new Slots\Slot(name: 'test_variable'),
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
