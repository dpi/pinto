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
    public function __construct(
    ) {
    }

    /**
     * @return array<class-string, class-string>
     *   Array of objects keyed by the extended object
     *
     * @internal
     */
    public static function discoverCanonicalProductObjectClasses(DefinitionDiscovery $definitionDiscovery): array
    {
        $lsbFactoryCanonicalObjectClasses = [];

        foreach ($definitionDiscovery as $objectClassName => $case) {
            // Only mark as extends if there is a #[CanonicalProduct] at any level,
            // even if a parent is a known theme object.
            if (CanonicalProduct::hasAttribute($objectClassName, $case)) {
                $extendsObject = $definitionDiscovery->extendsKnownObject($objectClassName);
                if (null !== $extendsObject) {
                    if (\array_key_exists($extendsObject, $lsbFactoryCanonicalObjectClasses)) {
                        throw new PintoMultipleCanonicalProduct($extendsObject, [$lsbFactoryCanonicalObjectClasses[$extendsObject], $objectClassName]);
                    }
                    $lsbFactoryCanonicalObjectClasses[$extendsObject] = $objectClassName;
                }
            }
        }

        return $lsbFactoryCanonicalObjectClasses;
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
