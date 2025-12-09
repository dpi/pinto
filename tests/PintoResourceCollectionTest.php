<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\Resource\ResourceCollection;
use Pinto\Resource\ResourceInterface;

/**
 * @coversDefaultClass \Pinto\Resource\ResourceCollection
 */
final class PintoResourceCollectionTest extends TestCase
{
    public function testImmutabilityOffsetSet(): void
    {
        $collection = ResourceCollection::create([]);
        static::expectException(\LogicException::class);
        static::expectExceptionMessage('Collection is immutable');
        $collection[] = $this->createMock(ResourceInterface::class);
    }

    public function testImmutabilityOffsetUnset(): void
    {
        $collection = ResourceCollection::create([]);
        static::expectException(\LogicException::class);
        static::expectExceptionMessage('Collection is immutable');
        unset($collection['does not exist']);
    }
}
