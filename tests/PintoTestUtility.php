<?php

declare(strict_types=1);

namespace Pinto\tests;

use Pinto\DefinitionCollection;
use Pinto\DefinitionDiscovery;
use Pinto\List\Resource\ObjectListEnumResource;
use Pinto\ObjectType\ObjectTypeDiscovery;

final class PintoTestUtility
{
    /**
     * @param class-string<\Pinto\List\ObjectListInterface> $objectListClassName
     *
     * @internal
     */
    public static function definitions(string $objectListClassName, DefinitionDiscovery $definitionDiscovery): DefinitionCollection
    {
        $collection = new DefinitionCollection();

        foreach ($objectListClassName::cases() as $case) {
            $objectClassName = $case->getClass();
            if (null !== $objectClassName) {
                $collection[$case] = ObjectTypeDiscovery::definitionForThemeObject($objectClassName, ObjectListEnumResource::createFromEnum($case), $definitionDiscovery)[1];
            }
        }

        return $collection;
    }
}
