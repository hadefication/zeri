<?php

declare(strict_types=1);

namespace Pest\Plugins\Actions;

use Pest\Contracts\Plugins;
use Pest\Plugin\Loader;




final class CallsTerminable
{





public static function execute(): void
{
$plugins = Loader::getPlugins(Plugins\Terminable::class);


foreach ($plugins as $plugin) {
$plugin->terminate();
}
}
}
