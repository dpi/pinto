<?php

declare(strict_types=1);

namespace Pinto\CanonicalProduct;

use Pinto\PintoMapping;

trait CanonicalFactoryTrait
{
    final public static function create(mixed ...$args): self
    {
        $className = self::pintoMappingStatic()->getCanonicalObjectClassName(self::class);
        if (null === $className) {
            return static::constructInstance(...$args);
        }

        if (self::class !== static::class) {
            // Maybe this should be opt-outable (attr and static cached optout).
            // It might be useful to turn this off in cases where an object is used in a list, then eventually is
            // updated to extend an object using this trait. In which case existing calls to the original object
            // would blow up here. But it shouldn't be too difficult to update all references?

            // In case the trait import renamed the method, get the correct method name:
            $realMethodCallName = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2)[0]['function'];
            throw new \BadMethodCallException(sprintf('Object factory should be called with `%s::%s` instead of `%s::%s`', self::class, $realMethodCallName, static::class, $realMethodCallName));
        }

        return $className::constructInstance(...$args);
    }

    protected static function constructInstance(mixed ...$args): static
    {
        // @phpstan-ignore-next-line
        return new static(...$args);
    }

    abstract private static function pintoMappingStatic(): PintoMapping;
}
