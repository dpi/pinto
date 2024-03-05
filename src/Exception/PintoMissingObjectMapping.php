<?php

declare(strict_types=1);

namespace Pinto\Exception;

final class PintoMissingObjectMapping extends \Exception
{
    /**
     * @param class-string $objectClassName
     */
    public function __construct(string $objectClassName, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('%s is not mapped to an object list. Add it to an object list enum.', $objectClassName), 0, $previous);
    }
}
