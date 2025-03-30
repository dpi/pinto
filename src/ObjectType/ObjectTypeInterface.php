<?php

declare(strict_types=1);

namespace Pinto\ObjectType;

use Pinto\List\ObjectListInterface;

/**
 * Defines an object type.
 */
interface ObjectTypeInterface
{
    public static function createBuild(ObjectListInterface $case, mixed $definition, string $objectClassName): mixed;

    public static function lateBindObjectToBuild(mixed $build, mixed $definition, object $object, LateBindObjectContext $context): void;

    /**
     * @phpstan-param class-string $objectClassName
     *
     * @throws \Pinto\Exception\Slots\BuildValidation
     */
    public static function validateBuild(mixed $build, mixed $definition, string $objectClassName): void;

    /**
     * @param ObjectListInterface $case
     *   The list enum case where the object is defined
     * @param \Reflector $r
     *   The location where this object type attribute (e.g Slots) was defined.
     */
    public function getDefinition(ObjectListInterface $case, \Reflector $r): mixed;
}
