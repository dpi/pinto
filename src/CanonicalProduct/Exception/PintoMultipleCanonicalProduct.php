<?php

declare(strict_types=1);

namespace Pinto\CanonicalProduct\Exception;

final class PintoMultipleCanonicalProduct extends \Exception
{
    /**
     * @param class-string $extendsObjectClassName
     * @param class-string[] $contestedObjectClassNames
     */
    public function __construct(string $extendsObjectClassName, array $contestedObjectClassNames, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(
            'Multiple objects are contested to override object `%s` where only one is permitted: %s', $extendsObjectClassName,
            implode(', ', $contestedObjectClassNames),
        ), 0, $previous);
    }
}
