<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use PHPUnit\TextUI\CliArguments\Builder as CliConfigurationBuilder;
use PHPUnit\TextUI\CliArguments\Exception as CliConfigurationException;
use PHPUnit\TextUI\CliArguments\XmlConfigurationFileFinder;
use PHPUnit\TextUI\XmlConfiguration\DefaultConfiguration;
use PHPUnit\TextUI\XmlConfiguration\Exception as XmlConfigurationException;
use PHPUnit\TextUI\XmlConfiguration\Loader;

/**
@no-named-arguments


*/
final readonly class Builder
{





public function build(array $argv): Configuration
{
try {
$cliConfiguration = (new CliConfigurationBuilder)->fromParameters($argv);
$configurationFile = (new XmlConfigurationFileFinder)->find($cliConfiguration);
$xmlConfiguration = DefaultConfiguration::create();

if ($configurationFile !== false) {
$xmlConfiguration = (new Loader)->load($configurationFile);
}

return Registry::init(
$cliConfiguration,
$xmlConfiguration,
);
} catch (CliConfigurationException|XmlConfigurationException $e) {
throw new ConfigurationCannotBeBuiltException(
$e->getMessage(),
$e->getCode(),
$e,
);
}
}
}
