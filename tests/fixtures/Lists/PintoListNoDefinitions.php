<?php

declare(strict_types=1);

namespace Pinto\tests\fixtures\Lists;

use Pinto\Attribute\Asset\Css;
use Pinto\Attribute\Asset\Js;
use Pinto\List\ObjectListInterface;
use Pinto\List\ObjectListTrait;

use function Safe\realpath;

enum PintoListNoDefinitions: string implements ObjectListInterface
{
    use ObjectListTrait;

    case No_Definition_No_Assets = 'no_definition_no_assets';

    #[Css('styles.css')]
    #[Js('app.js')]
    case No_Definition_With_Assets = 'no_definition_with_assets';

    public function templateDirectory(): string
    {
        return realpath(__DIR__ . '/../resources');
    }

    public function cssDirectory(): string
    {
        return realpath(__DIR__ . '/../resources/css');
    }

    public function jsDirectory(): string
    {
        return realpath(__DIR__ . '/../resources/javascript');
    }
}
