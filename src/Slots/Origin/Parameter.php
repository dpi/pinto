<?php

declare(strict_types=1);

namespace Pinto\Slots\Origin;

/**
 * @internal
 */
final class Parameter
{
    private function __construct(
        private readonly string $parameterName,
        private readonly string $functionName,
        private readonly string $className,
    ) {
    }

    public static function fromReflection(\ReflectionParameter $r): static
    {
        // Serialize it as best as possible.
        // The data here is not infinitely storable, e.g is reset on container resets.
        return new static(
            parameterName: $r->getName(),
            functionName: $r->getDeclaringFunction()->getName(),
            className: $r->getDeclaringClass()?->getName() ?? throw new \LogicException('unhandled'),
        );
    }

    public function parameterReflection(): \ReflectionParameter
    {
        try {
            return new \ReflectionParameter(
                [$this->className, $this->functionName],
                $this->parameterName,
            );
        } catch (\ReflectionException|\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(previous: $e);
        }
    }
}
