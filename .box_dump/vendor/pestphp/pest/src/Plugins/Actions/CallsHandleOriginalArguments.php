<?php

declare(strict_types=1);

namespace Pest\Plugins\Actions;

use Pest\Contracts\Plugins;
use Pest\Plugin\Loader;




final class CallsHandleOriginalArguments
{







public static function execute(array $argv): void
{
$plugins = Loader::getPlugins(Plugins\HandlesOriginalArguments::class);


foreach ($plugins as $plugin) {
$plugin->handleOriginalArguments($argv);
}
}
}
