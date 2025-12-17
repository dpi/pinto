<?php

declare(strict_types=1);

namespace Pinto;

use Pinto\Exception\PintoMissingObjectMapping;
use Pinto\Resource\ResourceCollection;
use Pinto\Resource\ResourceCollectionInterface;
use Pinto\Resource\ResourceInterface;

/**
 * Pinto mapping.
 */
final readonly class PintoMapping
{
    private ResourceCollectionInterface $resources;

    /**
     * @param array<string, ResourceInterface> $resources
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
        array $resources,
        private array $definitions,
        private array $buildInvokers,
        private array $types,
        private array $lsbFactoryCanonicalObjectClasses,
    ) {
        $this->resources = ResourceCollection::create($resources);
    }

    /**
     * @param class-string $objectClassName
     *
     * @throws PintoMissingObjectMapping
     */
    public function getResource(string $objectClassName): ResourceInterface
    {
        return $this->resources[$objectClassName] ?? throw new PintoMissingObjectMapping($objectClassName);
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
     * @throws PintoMissingObjectMapping
     */
    public function getBuilder(object $component): \Closure
    {
        $method = $this->buildInvokers[$component::class] ?? throw new PintoMissingObjectMapping($component::class);

        // @phpstan-ignore-next-line
        return $component->{$method}(...);
    }

    public function getResources(): ResourceCollectionInterface
    {
        return $this->resources;
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
