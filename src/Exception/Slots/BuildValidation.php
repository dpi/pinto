<?php

declare(strict_types=1);

namespace Pinto\Exception\Slots;

final class BuildValidation extends \Exception
{
    /**
     * @param class-string $objectClassName
     * @param string[] $missingSlots
     */
    public static function missingSlots(string $objectClassName, array $missingSlots): static
    {
        return new static(sprintf('Build for %s missing values for %s: `%s`', $objectClassName, 1 === count($missingSlots) ? 'slot' : 'slots', \implode('`, `', $missingSlots)));
    }
}
