<?php

declare(strict_types=1);

namespace Pest\Plugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Pest\Plugin\Commands\DumpCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;




final class Manager implements Capable, EventSubscriberInterface, PluginInterface
{



public const PLUGIN_CACHE_FILE = 'pest-plugins.json';




private $composer;




public function activate(Composer $composer, IOInterface $io): void
{
$this->composer = $composer;
}




public function uninstall(Composer $composer, IOInterface $io): void
{

$vendorDirectory = $composer->getConfig()->get('vendor-dir');
$pluginFile = sprintf('%s/%s', $vendorDirectory, self::PLUGIN_CACHE_FILE);

if (file_exists($pluginFile)) {
unlink($pluginFile);
}
}






public static function getSubscribedEvents()
{
return [
'post-autoload-dump' => 'registerPlugins',
];
}

public function getCapabilities()
{
return [
\Composer\Plugin\Capability\CommandProvider::class => PestCommandProvider::class,
];
}

public function registerPlugins(): void
{
$cmd = new DumpCommand;
$cmd->setComposer($this->composer);
$cmd->run(new ArrayInput([]), new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, true));
}


public function deactivate(Composer $composer, IOInterface $io): void {}
}
