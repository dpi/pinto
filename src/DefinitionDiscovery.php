<?php

declare(strict_types=1);

namespace Pinto;

use Ramsey\Collection\AbstractCollection;

/**
 * A mapping from theme object class-strings to the related enum case.
 *
 * @extends \Ramsey\Collection\AbstractCollection<class-string, \Pinto\List\ObjectListInterface>
 */
final class DefinitionDiscovery extends AbstractCollection
{
  public function getType(): string
  {
    return 'Pinto\\List\\ObjectListInterface';
  }

  /**
   * Utility for getting the first ancestor class-string of a class-string.
   *
   * @internal
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
