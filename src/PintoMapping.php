<?php

declare(strict_types=1);

namespace Pinto;

use Pinto\Exception\PintoMissingObjectMapping;
use Pinto\List\ObjectListInterface;

/**
 * Pinto mapping.
 */
final readonly class PintoMapping
{
    /**
     * @param array<class-string<\Pinto\List\ObjectListInterface>> $enumClasses
     * @param array<
     *   class-string,
     *   array{class-string<\Pinto\List\ObjectListInterface>, string}
     * > $enums
     * @param array<
     *   class-string,
     *   (array{'variables'?: array<mixed>}&array<string, mixed>)
     * > $themeDefinitions
     * @param array<class-string, string> $buildInvokers
     *
     * @internal
     */
    public function __construct(
        private array $enumClasses,
        private array $enums,
        private array $themeDefinitions,
        private array $buildInvokers,
    ) {
    }

    /**
     * @param class-string $objectClassName
     *
     * @throws PintoMissingObjectMapping
     */
    public function getByClass(string $objectClassName): ObjectListInterface
    {
        /** @var class-string<\Pinto\List\ObjectListInterface> $listClass */
        [$listClass, $caseName] = $this->enums[$objectClassName] ?? throw new PintoMissingObjectMapping($objectClassName);

        /** @var ObjectListInterface $enum */
        $enum = constant($listClass . '::' . $caseName);

        return $enum;
    }

    /**
     * @param class-string $objectClassName
     *
     * @return (array{'variables'?: array<mixed>}&array<string, mixed>)
     *
     * @throws PintoMissingObjectMapping
     */
    public function getThemeDefinition(string $objectClassName): array
    {
        return $this->themeDefinitions[$objectClassName] ?? throw new PintoMissingObjectMapping($objectClassName);
    }

    /**
     * @param class-string $objectClassName
     *
     * @throws PintoMissingObjectMapping
     */
    public function getBuildInvoker(string $objectClassName): string
    {
        return $this->buildInvokers[$objectClassName] ?? throw new PintoMissingObjectMapping($objectClassName);
    }

    /**
     * @return array<class-string<\Pinto\List\ObjectListInterface>>
     */
    public function getEnumClasses(): array
    {
        return $this->enumClasses;
    }
}
