<?php declare(strict_types=1);








namespace PHPUnit\Runner;

use function array_diff;
use function basename;
use function get_declared_classes;
use function realpath;
use function str_ends_with;
use function strpos;
use function strtolower;
use function substr;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
@no-named-arguments


*/
final class TestSuiteLoader
{



private static array $declaredClasses = [];




private static array $fileToClassesMap = [];






public function load(string $suiteClassFile): ReflectionClass
{
$suiteClassFile = realpath($suiteClassFile);
$suiteClassName = $this->classNameFromFileName($suiteClassFile);
$loadedClasses = $this->loadSuiteClassFile($suiteClassFile);

foreach ($loadedClasses as $className) {
/**
@noinspection */
$class = new ReflectionClass($className);

if ($class->isAnonymous()) {
continue;
}

if ($class->getFileName() !== $suiteClassFile) {
continue;
}

if (!$class->isSubclassOf(TestCase::class)) {
continue;
}

if (!str_ends_with(strtolower($class->getShortName()), strtolower($suiteClassName))) {
continue;
}

if (!$class->isAbstract()) {
return $class;
}

$e = new ClassIsAbstractException($class->getName(), $suiteClassFile);
}

if (isset($e)) {
throw $e;
}

foreach ($loadedClasses as $className) {
if (str_ends_with(strtolower($className), strtolower($suiteClassName))) {
throw new ClassDoesNotExtendTestCaseException($className, $suiteClassFile);
}
}

throw new ClassCannotBeFoundException($suiteClassName, $suiteClassFile);
}

private function classNameFromFileName(string $suiteClassFile): string
{
$className = basename($suiteClassFile, '.php');
$dotPos = strpos($className, '.');

if ($dotPos !== false) {
$className = substr($className, 0, $dotPos);
}

return $className;
}




private function loadSuiteClassFile(string $suiteClassFile): array
{
if (isset(self::$fileToClassesMap[$suiteClassFile])) {
return self::$fileToClassesMap[$suiteClassFile];
}

if (empty(self::$declaredClasses)) {
self::$declaredClasses = get_declared_classes();
}

require_once $suiteClassFile;

$loadedClasses = array_diff(
get_declared_classes(),
self::$declaredClasses,
);

foreach ($loadedClasses as $loadedClass) {
/**
@noinspection */
$class = new ReflectionClass($loadedClass);

if (!isset(self::$fileToClassesMap[$class->getFileName()])) {
self::$fileToClassesMap[$class->getFileName()] = [];
}

self::$fileToClassesMap[$class->getFileName()][] = $class->getName();
}

self::$declaredClasses = get_declared_classes();

if (empty($loadedClasses)) {
return self::$declaredClasses;
}

return $loadedClasses;
}
}
