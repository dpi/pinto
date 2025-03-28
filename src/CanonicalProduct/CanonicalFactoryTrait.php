<?php

declare(strict_types = 1);

namespace Pinto\CanonicalProduct;

use Pinto\Attribute\Build;
use Pinto\PintoMapping;

trait CanonicalFactoryTrait {

  #[Build]
  final public static function create(...$args): self {
    $className = static::staticPintoMapping()->getCanonicalObjectClassName(self::class);
    if ($className === NULL) {
      return static::constructInstance(...$args);
    }

    if (static::class !== self::class) {
      throw new \BadMethodCallException(sprintf('Object factory should be called with `%s::%s` instead of `%s::%s`', self::class, __FUNCTION__, static::class, __FUNCTION__));
    }

    return $className::constructInstance(...$args);
  }

  protected static function constructInstance(...$args): static {
    return new static(...$args);
  }

  static private function staticPintoMapping(): PintoMapping {
    return \Drupal::service(PintoMapping::class);
  }

}
