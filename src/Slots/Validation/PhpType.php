<?php

declare(strict_types=1);

namespace Pinto\Slots\Validation;

use Nette\Utils\Type;

/**
 * Represents validation against a PHP type.
 *
 * @internal
 */
final class PhpType
{
    private function __construct(
        public readonly string $type,
    ) {
    }

    public static function fromReflection(\ReflectionParameter $r): static
    {
        return new static((string) Type::fromReflection($r));
    }

    public static function fromString(string $string): static
    {
        return new static($string);
    }
}
