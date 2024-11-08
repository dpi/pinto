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

    public static function lateBindObjectToBuild(mixed $build, mixed $definition, object $object): void;

    /**
     * @phpstan-param class-string $objectClassName
     *
     * @throws \Pinto\Exception\Slots\BuildValidation
     */
    public static function validateBuild(mixed $build, mixed $definition, string $objectClassName): void;

    public function getDefinition(ObjectListInterface $case, \Reflector $r): mixed;
}
