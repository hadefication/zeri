<?php declare(strict_types=1);








namespace PHPUnit\Util;

use const PHP_OS_FAMILY;
use function class_exists;
use function defined;
use function dirname;
use function is_dir;
use function realpath;
use function str_starts_with;
use function sys_get_temp_dir;
use Composer\Autoload\ClassLoader;
use DeepCopy\DeepCopy;
use PharIo\Manifest\Manifest;
use PharIo\Version\Version as PharIoVersion;
use PhpParser\Parser;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SebastianBergmann\CliParser\Parser as CliParser;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeUnit\CodeUnit;
use SebastianBergmann\CodeUnitReverseLookup\Wizard;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Complexity\Calculator;
use SebastianBergmann\Diff\Diff;
use SebastianBergmann\Environment\Runtime;
use SebastianBergmann\Exporter\Exporter;
use SebastianBergmann\FileIterator\Facade as FileIteratorFacade;
use SebastianBergmann\GlobalState\Snapshot;
use SebastianBergmann\Invoker\Invoker;
use SebastianBergmann\LinesOfCode\Counter;
use SebastianBergmann\ObjectEnumerator\Enumerator;
use SebastianBergmann\ObjectReflector\ObjectReflector;
use SebastianBergmann\RecursionContext\Context;
use SebastianBergmann\Template\Template;
use SebastianBergmann\Timer\Timer;
use SebastianBergmann\Type\TypeName;
use SebastianBergmann\Version;
use staabm\SideEffectsDetector\SideEffectsDetector;
use TheSeer\Tokenizer\Tokenizer;

/**
@no-named-arguments
*/
final class ExcludeList
{



private const EXCLUDED_CLASS_NAMES = [

ClassLoader::class => 1,


DeepCopy::class => 1,


Parser::class => 1,


Manifest::class => 1,


PharIoVersion::class => 1,


TestCase::class => 2,


CodeCoverage::class => 1,


FileIteratorFacade::class => 1,


Invoker::class => 1,


Template::class => 1,


Timer::class => 1,


CliParser::class => 1,


CodeUnit::class => 1,


Wizard::class => 1,


Comparator::class => 1,


Calculator::class => 1,


Diff::class => 1,


Runtime::class => 1,


Exporter::class => 1,


Snapshot::class => 1,


Counter::class => 1,


Enumerator::class => 1,


ObjectReflector::class => 1,


Context::class => 1,


TypeName::class => 1,


Version::class => 1,


SideEffectsDetector::class => 1,


Tokenizer::class => 1,
];




private static array $directories = [];
private static bool $initialized = false;
private readonly bool $enabled;






public static function addDirectory(string $directory): void
{
if (!is_dir($directory)) {
throw new InvalidDirectoryException($directory);
}

self::$directories[] = realpath($directory);
}

public function __construct(?bool $enabled = null)
{
if ($enabled === null) {
$enabled = !defined('PHPUNIT_TESTSUITE');
}

$this->enabled = $enabled;
}




public function getExcludedDirectories(): array
{
self::initialize();

return self::$directories;
}

public function isExcluded(string $file): bool
{
if (!$this->enabled) {
return false;
}

self::initialize();

foreach (self::$directories as $directory) {
if (str_starts_with($file, $directory)) {
return true;
}
}

return false;
}

private static function initialize(): void
{
if (self::$initialized) {
return;
}

foreach (self::EXCLUDED_CLASS_NAMES as $className => $parent) {
if (!class_exists($className)) {
continue;
}

$directory = (new ReflectionClass($className))->getFileName();

for ($i = 0; $i < $parent; $i++) {
$directory = dirname($directory);
}

self::$directories[] = $directory;
}







if (PHP_OS_FAMILY === 'Windows') {

self::$directories[] = sys_get_temp_dir() . '\\PHP';

}

self::$initialized = true;
}
}
