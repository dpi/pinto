<?php

declare(strict_types=1);

namespace Pinto\CanonicalProduct\Attribute;

use Pinto\CanonicalProduct\Exception\PintoMultipleCanonicalProduct;
use Pinto\DefinitionDiscovery;
use Pinto\List\ObjectListInterface;

/**
 * An attribute for representing whether an object replaces another when the canonical factory is used.
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS)]
final class CanonicalProduct
{
    /**
     * Class is never instantiated; functionality comes from placement.
     *
     * @codeCoverageIgnore
     */
    public function __construct(
    ) {
    }

    /**
     * Determine canonical class produced from factories.
     *
     * Classes may nominate themselves as the product produced when a class is
     * produced from a factory at a root (extended) class.
     *
     * @return array<class-string, class-string>
     *   Objects strings keyed by the extended object.
     *   The result of this method is designed to be consumed by
     *   \Pinto\PintoMapping::__construct(lsbFactoryCanonicalObjectClasses)
     *
     * @internal
     */
    public static function discoverCanonicalProductObjectClasses(DefinitionDiscovery $definitionDiscovery): array
    {
        // If at any point a class extends a known object which extends another known object, the
        // intermediate objects are eliminated from the tree.
        // If more than one object remains for a factory class, then throw an exception.

        /** @var class-string[] $eliminate */
        $eliminate = [];
        /** @var array<class-string, class-string> $keep */
        $keep = [];
        foreach ($definitionDiscovery as $objectClassName => $case) {
            if (false === CanonicalProduct::hasAttribute($objectClassName, $case)) {
                continue;
            }

            /** @var class-string[] $extends */
            $extends = [];
            $extendsObject = $objectClassName;
            while (null !== ($extendsObject = $definitionDiscovery->extendsKnownObject($extendsObject))) {
                $extends[] = $extendsObject;

                // End on this object if it isn't a CanonicalProduct:
                if (false === static::hasAttribute($extendsObject, $definitionDiscovery[$extendsObject])) {
                    break;
                }
            }

            // Only keep the last (root-most).
            if ([] !== $extends) {
                $keep[$objectClassName] = \array_pop($extends);
            }

            // If there are any intermediate classes, mark for elimination.
            \array_push($eliminate, ...$extends);
        }

        // Remove any intermediate objects:
        $keep = \array_diff_key($keep, \array_flip($eliminate));

        // Determine which root items have duplicate associated child classes:
        /** @var array<class-string, int<1, max>> $extendsObjectByUseCount */
        $extendsObjectByUseCount = array_count_values($keep);

        // Filter out singles.
        // If any items remain, an exception is guaranteed.
        $extendsObjectUsedMultipleTimes = \array_diff($extendsObjectByUseCount, [1]);

        // There may be multiple problems, throw on first.
        foreach (array_keys($extendsObjectUsedMultipleTimes) as $extendsObject) {
            /** @var class-string[] $objectClassNames */
            $objectClassNames = array_keys($keep, $extendsObject, true);
            throw new PintoMultipleCanonicalProduct($extendsObject, $objectClassNames);
        }

        // Flip around object->root to the other way.
        return \array_flip($keep);
    }

    /**
     * Discovers CanonicalProduct attributes on a class or the related enum.
     *
     * In order:
     *   - On the class.
     *   - On the enum.
     *
     * @param class-string $objectClassName
     */
    private static function hasAttribute(string $objectClassName, ObjectListInterface $case): bool
    {
        foreach ([
            // Look for attribute instances of ObjectTypeInterface on the class itself.
            $objectClassName,
            // Then the enum.
            $case::class,
        ] as $className) {
            $objectClassReflection = new \ReflectionClass($className);
            if ([] !== $objectClassReflection->getAttributes(static::class)) {
                return true;
            }
        }

        return false;
    }
}
