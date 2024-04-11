<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\Js;
use Pinto\Attribute\DependencyOn;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;

enum PintoListDependencies: string implements ObjectListInterface
{
    use ObjectListTrait;

    /**
     * Tests multiple are merged.
     */
    #[DependencyOn(self::Beta)]
    #[DependencyOn(self::Charlie)]
    case Alpha = 'alpha';

    #[Css('styles.css')]
    #[Js('app.js')]
    #[DependencyOn(self::Charlie)]
    case Beta = 'beta';

    #[Js('app.js')]
    case Charlie = 'charlie';

    #[DependencyOn(self::Alpha)]
    case Delta = 'delta';

    public function templateDirectory(): string
    {
        return 'tests/fixtures/resources';
    }

    public function cssDirectory(): string
    {
        return 'tests/fixtures/resources';
    }

    public function jsDirectory(): string
    {
        return 'tests/fixtures/resources';
    }
}
