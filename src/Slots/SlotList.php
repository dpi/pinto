<?php

declare(strict_types=1);

namespace Pinto\Slots;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends \Ramsey\Collection\AbstractCollection<\Pinto\Slots\Slot>
 */
final class SlotList extends AbstractCollection
{
    public function getType(): string
    {
        return 'Pinto\\Slots\\Slot';
    }
}
