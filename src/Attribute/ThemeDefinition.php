<?php

declare(strict_types=1);

namespace Pinto\Attribute;

use Pinto\Attribute\ObjectType\LegacyThemeDefinitionTrait;
use Pinto\ObjectType\ObjectTypeInterface;

/**
 * An attribute representing the theme definition.
 *
 * - When attached to a class, the value of $definition must be set.
 * - When attached to a method, the $definition must not be set, the definition
 *   is instead returned by the method.
 *
 * The definition is merged into the default hook_theme definition.
 *
 * There is no need to define `template` or `path` key, as these are provided.
 * Though you may choose to override.
 *
 * @internal use \Pinto\Attribute\ObjectType\Slots instead
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
final class ThemeDefinition implements ObjectTypeInterface
{
    use LegacyThemeDefinitionTrait;
}
