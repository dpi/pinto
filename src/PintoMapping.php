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
     * @param array<class-string<ObjectListInterface>> $enumClasses
     * @param array<
     *   class-string,
     *   array{class-string<ObjectListInterface>, string}
     * > $enums
     * @param array<class-string, mixed> $definitions
     * @param array<class-string, string> $buildInvokers
     * @param array<class-string, class-string<ObjectType\ObjectTypeInterface>> $types
     *   A map of object class-strings to object type class-strings
     * @param array<class-string, class-string> $lsbFactoryCanonicalObjectClasses
     *   A map of original object class-string to overridden class-strings. Used
     *   by static factories on a base class, where a child class wants to indicate it overrides
     *   the base class.
     *
     * @internal
     */
    public function __construct(
        private array $enumClasses,
        private array $enums,
        private array $definitions,
        private array $buildInvokers,
        private array $types,
        private array $lsbFactoryCanonicalObjectClasses,
    ) {
    }

    /**
     * Get the enum case.
     *
     * @param class-string $objectClassName
     *
     * @throws PintoMissingObjectMapping
     */
    public function getByClass(string $objectClassName): ObjectListInterface
    {
        /** @var class-string<ObjectListInterface> $listClass */
        [$listClass, $caseName] = $this->enums[$objectClassName] ?? throw new PintoMissingObjectMapping($objectClassName);

        /** @var ObjectListInterface $enum */
        $enum = constant($listClass . '::' . $caseName);

        return $enum;
    }

    /**
     * @param class-string $objectClassName
     *
     * @throws PintoMissingObjectMapping
     */
    public function getThemeDefinition(string $objectClassName): mixed
    {
        return $this->definitions[$objectClassName] ?? throw new PintoMissingObjectMapping($objectClassName);
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
     * @return array<class-string<ObjectListInterface>>
     */
    public function getEnumClasses(): array
    {
        return $this->enumClasses;
    }

    /**
     * @param class-string $objectClassName
     *
     * @return class-string<ObjectType\ObjectTypeInterface>
     *
     * @throws PintoMissingObjectMapping
     */
    public function getObjectType(string $objectClassName): string
    {
        return $this->types[$objectClassName] ?? throw new PintoMissingObjectMapping($objectClassName);
    }

    public function getCanonicalObjectClassName(string $rootObjectClassName): ?string
    {
        return $this->lsbFactoryCanonicalObjectClasses[$rootObjectClassName] ?? null;
    }

    /**
     * @phpstan-return class-string
     */
    public function getFactoryOfCanonicalObject(string $objectClassName): ?string
    {
        $key = \array_search($objectClassName, $this->lsbFactoryCanonicalObjectClasses, true);

        return false !== $key ? $key : null;
    }
}
