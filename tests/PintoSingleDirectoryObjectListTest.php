<?php

declare(strict_types=1);

namespace Pinto\tests;

use PHPUnit\Framework\TestCase;
use Pinto\tests\fixtures\Lists\PintoSingleDirectoryObjectList;

/**
 * @covers \Pinto\List\SingleDirectoryObjectListTrait
 */
final class PintoSingleDirectoryObjectListTest extends TestCase
{
    public function testSlotsAttribute(): void
    {
        static::assertEquals('/a/directory/with/shared/resources', PintoSingleDirectoryObjectList::SDO->templateDirectory());
        static::assertEquals('/a/directory/with/shared/resources', PintoSingleDirectoryObjectList::SDO->cssDirectory());
        static::assertEquals('/a/directory/with/shared/resources', PintoSingleDirectoryObjectList::SDO->jsDirectory());
    }
}
