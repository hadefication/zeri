<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Command;

use const PHP_EOL;
use const STDIN;
use function assert;
use function defined;
use function fgets;
use function file_put_contents;
use function getcwd;
use function is_file;
use function sprintf;
use function trim;
use PHPUnit\Runner\Version;
use PHPUnit\TextUI\XmlConfiguration\Generator;

/**
@no-named-arguments


*/
final readonly class GenerateConfigurationCommand implements Command
{
public function execute(): Result
{
$directory = getcwd();

print 'Generating phpunit.xml in ' . $directory . PHP_EOL . PHP_EOL;
print 'Bootstrap script (relative to path shown above; default: vendor/autoload.php): ';

$bootstrapScript = $this->read();

print 'Tests directory (relative to path shown above; default: tests): ';

$testsDirectory = $this->read();

print 'Source directory (relative to path shown above; default: src): ';

$src = $this->read();

print 'Cache directory (relative to path shown above; default: .phpunit.cache): ';

$cacheDirectory = $this->read();

if ($bootstrapScript === '') {
$bootstrapScript = 'vendor/autoload.php';
}

if ($testsDirectory === '') {
$testsDirectory = 'tests';
}

if ($src === '') {
$src = 'src';
}

if ($cacheDirectory === '') {
$cacheDirectory = '.phpunit.cache';
}

if (defined('PHPUNIT_COMPOSER_INSTALL') &&
is_file($directory . '/vendor/phpunit/phpunit/phpunit.xsd')) {
$schemaLocation = 'vendor/phpunit/phpunit/phpunit.xsd';
} else {
$schemaLocation = sprintf(
'https://schema.phpunit.de/%s/phpunit.xsd',
Version::series(),
);
}

$generator = new Generator;

$result = @file_put_contents(
$directory . '/phpunit.xml',
$generator->generateDefaultConfiguration(
$schemaLocation,
$bootstrapScript,
$testsDirectory,
$src,
$cacheDirectory,
),
);

if ($result !== false) {
return Result::from(
sprintf(
PHP_EOL . 'Generated phpunit.xml in %s.' . PHP_EOL .
'Make sure to exclude the %s directory from version control.' . PHP_EOL,
$directory,
$cacheDirectory,
),
);
}


return Result::from(
sprintf(
PHP_EOL . 'Could not write phpunit.xml in %s.' . PHP_EOL,
$directory,
),
Result::EXCEPTION,
);

}

private function read(): string
{
$buffer = fgets(STDIN);

assert($buffer !== false);

return trim($buffer);
}
}
