<?php

declare(strict_types=1);

namespace Pinto\ThemeDefinition;

/**
 * @internal
 */
final class HookThemeDefinition
{
    /**
     * @param array{variables?: array<mixed>} $definition
     */
    public function __construct(
        public array $definition,
    ) {
    }
}
