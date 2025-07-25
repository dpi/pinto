<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Objects\Slots;

use Pinto\Object\ObjectTrait;
use Pinto\PintoMapping;
use Pinto\Slots;

/**
 * Slots from parent.
 */
final class PintoObjectSlotsByInheritanceChild extends PintoObjectSlotsByInheritanceParent
{
    use ObjectTrait;

    public function __construct(
        public readonly string $fooFromChild,
    ) {
        parent::__construct('');
    }

    public function __invoke(): mixed
    {
        throw new \LogicException('Object level logic not tested.');
    }

    private function pintoMapping(): PintoMapping
    {
        throw new \LogicException('Object level logic not tested.');
    }
}
