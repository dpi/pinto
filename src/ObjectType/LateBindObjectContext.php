<?php

declare(strict_types=1);

namespace Pinto\ObjectType;

use Pinto\Exception\PintoMissingObjectMapping;
use Pinto\PintoMapping;

/**
 * @internal
 */
final class LateBindObjectContext
{
    private function __construct(
        private PintoMapping $pintoMapping,
    ) {
    }

    /**
     * @internal
     */
    public static function create(PintoMapping $pintoMapping): static
    {
        return new static($pintoMapping);
    }

    public function getBuilder(object $component): ?\Closure
    {
        try {
            return $this->pintoMapping->getBuilder($component);
        } catch (PintoMissingObjectMapping) {
            return null;
        }
    }
}
