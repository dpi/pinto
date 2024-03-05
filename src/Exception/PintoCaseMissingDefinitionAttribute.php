<?php

declare(strict_types=1);

namespace Pinto\Exception;

use Pinto\Attribute\Definition;

final class PintoCaseMissingDefinitionAttribute extends \Exception
{
    /**
     * @param class-string<\Pinto\List\ObjectListInterface> $objectListClass
     */
    public function __construct(string $objectListClass, string $caseName, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(
            sprintf('Case missing `%s::%s` %s attribute.', $objectListClass, $caseName, Definition::class),
        ), 0, $previous);
    }
}
