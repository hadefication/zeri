<?php declare(strict_types=1);








namespace PHPUnit\TextUI\CliArguments;

use function getcwd;
use function is_dir;
use function is_file;
use function realpath;

/**
@no-named-arguments


*/
final readonly class XmlConfigurationFileFinder
{
public function find(Configuration $configuration): false|string
{
$useDefaultConfiguration = $configuration->useDefaultConfiguration();

if ($configuration->hasConfigurationFile()) {
if (is_dir($configuration->configurationFile())) {
$candidate = $this->configurationFileInDirectory($configuration->configurationFile());

if ($candidate !== false) {
return $candidate;
}

return false;
}

return $configuration->configurationFile();
}

if ($useDefaultConfiguration) {
$directory = getcwd();

if ($directory !== false) {
$candidate = $this->configurationFileInDirectory($directory);

if ($candidate !== false) {
return $candidate;
}
}
}

return false;
}

private function configurationFileInDirectory(string $directory): false|string
{
$candidates = [
$directory . '/phpunit.xml',
$directory . '/phpunit.dist.xml',
$directory . '/phpunit.xml.dist',
];

foreach ($candidates as $candidate) {
if (is_file($candidate)) {
return realpath($candidate);
}
}

return false;
}
}
