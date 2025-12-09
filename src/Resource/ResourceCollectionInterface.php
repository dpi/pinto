<?php

declare(strict_types=1);

namespace Pinto\Resource;

/**
 * @internal
 *
 * @extends \IteratorAggregate<array-key, ResourceInterface>
 * @extends \ArrayAccess<array-key, ResourceInterface>
 */
interface ResourceCollectionInterface extends \IteratorAggregate, \ArrayAccess
{
}
