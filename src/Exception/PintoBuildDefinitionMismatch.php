<?php

declare(strict_types=1);

namespace Pinto\Exception;

final class PintoBuildDefinitionMismatch extends \Exception
{
    /**
     * @param class-string $objectClassName
     * @param string[] $missingKeys
     */
    public function __construct(string $objectClassName, array $missingKeys, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(
            'Build for %s is missing keys: %s', $objectClassName,
            implode(', ', $missingKeys),
        ), 0, $previous);
    }
}
