<?php

declare(strict_types=1);

namespace Pinto;

use Ramsey\Collection\AbstractCollection;
use Ramsey\Collection\Map\AbstractMap;

/**
 * A mapping from theme object class-strings to the related enum case.
 *
 * @extends AbstractMap<class-string,\Pinto\List\ObjectListInterface>
 */
final class DefinitionDiscovery extends AbstractMap
{

  /**
   * Utility for getting the first ancestor class-string of a class-string.
   *
   * @internal
   *
   * @phpstan-param class-string $objectClassName
   * @phpstan-return class-string
   */
  public function extendsKnownObject(string $objectClassName): ?string
  {
    $r = new \ReflectionClass($objectClassName);
    $parent = NULL;

    while (FALSE !== ($parent = ($parent ?? $r)->getParentClass())) {
      $parentClassName = $parent->getName();
      if ($this->offsetExists($parentClassName)) {
        return $parentClassName;
      }
    }

    return null;
  }

}
