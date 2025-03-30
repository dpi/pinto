<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists\AssetGlob;

use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\Js;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;

enum PintoListAssetGlob implements ObjectListInterface
{
    use ObjectListTrait;

    #[Css('styles*.css')]
    #[Js('script*.js')]
    case Wildcard;

    public function templateDirectory(): string
    {
        throw new \LogicException('Not tested.');
    }

    public function cssDirectory(): string
    {
        return 'tests/fixtures/Assets/PintoListAssetGlob';
    }

    public function jsDirectory(): string
    {
        return 'tests/fixtures/Assets/PintoListAssetGlob';
    }
}
