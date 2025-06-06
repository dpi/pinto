<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists\DependencyOn;

use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\Js;
use Pinto\Attribute\DependencyOn;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;

use function Safe\realpath;

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
    #[DependencyOn('foo/bar')]
    case Delta = 'delta';

    public function templateDirectory(): string
    {
        return 'tests/fixtures/resources';
    }

    public function cssDirectory(): string
    {
        return realpath(__DIR__ . '/../../resources/css');
    }

    public function jsDirectory(): string
    {
        return realpath(__DIR__ . '/../../resources/javascript');
    }
}
