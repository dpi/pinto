<?php

declare(strict_types=1);

namespace Pinto\Attribute;

use Pinto\Exception\PintoThemeDefinition;

/**
 * An attribute representing the theme definition.
 *
 * - When attached to a class, the value of $definition must be set.
 * - When attached to a method, the $definition must not be set, the definition
 *   is instead returned by the method.
 *
 * The definition is merged into the default hook_theme definition.
 *
 * There is no need to define `template` or `path key, as these is provided.
 * Though you may choose to override.
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class ThemeDefinition
{
    /**
     * Defines a theme definition.
     *
     * @param array<mixed>|null $definition
     *   Definition required when attached to a class. Otherwise, must be NULL.
     */
    public function __construct(
        public ?array $definition = null,
    ) {
    }

    /**
     * @param class-string $objectClassName
     *
     * @return array<mixed>
     *
     * @throws PintoThemeDefinition
     */
    public static function themeDefinitionForThemeObject(string $objectClassName): array
    {
        /** @var array<array<mixed>> $objThemeDefinitions */
        $objThemeDefinitions = [];
        $objectClassReflection = new \ReflectionClass($objectClassName);
        array_push($objThemeDefinitions, ...array_map(function (\ReflectionAttribute $r) {
            /** @var static $themeDefinition */
            $themeDefinition = $r->newInstance();

            return $themeDefinition->definition ?? throw new PintoThemeDefinition('Theme definition must be set for theme definition attributes on the class level of a theme object.');
        }, $objectClassReflection->getAttributes(static::class)));

        $methods = $objectClassReflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $reflectionMethod) {
            array_push($objThemeDefinitions, ...array_map(static function (\ReflectionAttribute $r) use ($reflectionMethod) {
                /** @var static $themeDefinition */
                $themeDefinition = $r->newInstance();
                if (null !== $themeDefinition->definition) {
                    throw new PintoThemeDefinition(sprintf('%s attribute must not have $definition set on %s::%s.', static::class, $reflectionMethod->getDeclaringClass()->getName(), $reflectionMethod->getName()));
                }

                if (false === $reflectionMethod->isStatic()) {
                    throw new PintoThemeDefinition(sprintf('%s attribute must be attached to a static method. %s::%s is not static.', static::class, $reflectionMethod->getDeclaringClass()->getName(), $reflectionMethod->getName()));
                }

                // Call the method directly and get the theme definition.
                /** @var array<mixed> $result */
                $result = $reflectionMethod->invoke(null);

                return $result;
            }, $reflectionMethod->getAttributes(static::class)));
        }

        if (0 === count($objThemeDefinitions)) {
            throw new PintoThemeDefinition(sprintf('Missing %s attribute on %s', static::class, $objectClassName));
        } elseif (count($objThemeDefinitions) > 1) {
            throw new PintoThemeDefinition(sprintf('Multiple theme definitions found on %s. There must only be one.', $objectClassName));
        }

        return $objThemeDefinitions[0];
    }
}
