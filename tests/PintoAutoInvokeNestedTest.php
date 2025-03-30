<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pinto\Slots\Build;
use Pinto\tests\fixtures\Objects\AutoInvokeNested\PintoObjectAutoInvokeContainer;

final class PintoAutoInvokeNestedTest extends TestCase
{
    /**
     * Test auto-invoking builds of nested objects.
     *
     * Where nesting fixtures are arranged as:
     *
     * Container
     * ├─ Child1
     * └─ Child2
     *    └─ Child3
     *
     * @see PintoObjectAutoInvokeContainer
     * @see Pinto\tests\fixtures\Objects\AutoInvokeNested\PintoObjectAutoInvokeChild1
     * @see Pinto\tests\fixtures\Objects\AutoInvokeNested\PintoObjectAutoInvokeChild2
     * @see Pinto\tests\fixtures\Objects\AutoInvokeNested\PintoObjectAutoInvokeChild3
     */
    public function testNested(): void
    {
        $object = new PintoObjectAutoInvokeContainer(foo: 'Text in Container');
        $build = $object();

        // PintoObjectAutoInvokeContainer build and slot values.
        static::assertInstanceOf(Build::class, $build);
        static::assertEquals('Text in Container', $build->pintoGet('text'));

        // PintoObjectAutoInvokeChild1 build and slot values.
        static::assertInstanceOf(Build::class, $build->pintoGet('child_1'));
        static::assertEquals('Text in Child1', $build->pintoGet('child_1')->pintoGet('child1_text'));
        static::assertInstanceOf(Build::class, $build->pintoGet('child_2'));

        // PintoObjectAutoInvokeChild2 build and slot values.
        static::assertEquals('Text in Child2', $build->pintoGet('child_2')->pintoGet('child2_text'));
        static::assertInstanceOf(Build::class, $build->pintoGet('child_2')->pintoGet('child2_child'));

        // PintoObjectAutoInvokeChild3 build and slot values.
        static::assertEquals('Text in Child3', $build->pintoGet('child_2')->pintoGet('child2_child')->pintoGet('child3_text'));
    }
}
