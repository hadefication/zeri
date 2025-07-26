<?php

namespace Illuminate\Foundation;

use Composer\Script\Event;

class ComposerScripts
{






public static function postInstall(Event $event)
{
require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

static::clearCompiled();
}







public static function postUpdate(Event $event)
{
require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

static::clearCompiled();
}







public static function postAutoloadDump(Event $event)
{
require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

static::clearCompiled();
}






protected static function clearCompiled()
{
$laravel = new Application(getcwd());

if (is_file($configPath = $laravel->getCachedConfigPath())) {
@unlink($configPath);
}

if (is_file($servicesPath = $laravel->getCachedServicesPath())) {
@unlink($servicesPath);
}

if (is_file($packagesPath = $laravel->getCachedPackagesPath())) {
@unlink($packagesPath);
}
}
}
