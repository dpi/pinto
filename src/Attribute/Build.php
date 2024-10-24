<?php

declare(strict_types=1);

namespace Pinto\Attribute;

use Pinto\Exception\PintoBuild;

/**
 * An attribute representing the build.
 *
 * Optional attribute designating a method providing a build function. If this
 * attribute is not used on any method of an object, the __invoke method is
 * used.
 *
 * The method associated with builder will return a renderable result. Usually
 * an array or another object.
 *
 * This method SHOULD only be invoked via an instance of this class.
 *
 * A trait is available at ObjectTrait to add defaults, abstraction, and
 * safety checks.
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class Build
{
    /**
     * @param class-string $objectClassName
     *
     * @throws PintoBuild
     */
    public static function buildMethodForThemeObject(string $objectClassName): string
    {
        $buildMethods = [];

        $objectClassReflection = new \ReflectionClass($objectClassName);
        $methods = $objectClassReflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $reflectionMethod) {
            foreach ($reflectionMethod->getAttributes(static::class) as $buildAttr) {
                $buildMethods[] = $reflectionMethod->getName();
            }
        }

        if (0 === count($buildMethods) && $objectClassReflection->hasMethod('__invoke')) {
            $buildMethods[] = '__invoke';
        }

        if (0 === count($buildMethods)) {
            throw new PintoBuild(sprintf('Missing %s attribute or __invoke() method on %s', static::class, $objectClassName));
        } elseif (count($buildMethods) > 1) {
            throw new PintoBuild(sprintf('Multiple build definitions found on %s. There must only be one.', $objectClassName));
        }

        return $buildMethods[0];
    }
}
