<?php

declare(strict_types=1);

namespace Pinto\ObjectType;

use Pinto\Exception\PintoMissingObjectMapping;
use Pinto\PintoMapping;

/**
 * Discovers ObjectTypeInterface attributes on a class, on a class or its methods.
 *
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

    /**
     * @param class-string $objectClassName
     */
    public function getBuildInvoker(string $objectClassName): ?string
    {
        try {
            return $this->pintoMapping->getBuildInvoker($objectClassName);
        } catch (PintoMissingObjectMapping) {
            return null;
        }
    }
}
