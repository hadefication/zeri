<?php

declare(strict_types=1);

namespace Pest\Plugins\Actions;

use Pest\Contracts\Plugins;
use Pest\Plugin\Loader;




final class CallsHandleArguments
{








public static function execute(array $argv): array
{
$plugins = Loader::getPlugins(Plugins\HandlesArguments::class);


foreach ($plugins as $plugin) {
$argv = $plugin->handleArguments($argv);
}

return $argv;
}
}
