<?php










namespace Symfony\Component\ErrorHandler\ErrorEnhancer;

use Composer\Autoload\ClassLoader;
use Symfony\Component\ErrorHandler\DebugClassLoader;
use Symfony\Component\ErrorHandler\Error\ClassNotFoundError;
use Symfony\Component\ErrorHandler\Error\FatalError;




class ClassNotFoundErrorEnhancer implements ErrorEnhancerInterface
{
public function enhance(\Throwable $error): ?\Throwable
{

$message = !$error instanceof FatalError ? $error->getMessage() : $error->getError()['message'];
if (!preg_match('/^(Class|Interface|Trait) [\'"]([^\'"]+)[\'"] not found$/', $message, $matches)) {
return null;
}
$typeName = strtolower($matches[1]);
$fullyQualifiedClassName = $matches[2];

if (false !== $namespaceSeparatorIndex = strrpos($fullyQualifiedClassName, '\\')) {
$className = substr($fullyQualifiedClassName, $namespaceSeparatorIndex + 1);
$namespacePrefix = substr($fullyQualifiedClassName, 0, $namespaceSeparatorIndex);
$message = \sprintf('Attempted to load %s "%s" from namespace "%s".', $typeName, $className, $namespacePrefix);
$tail = ' for another namespace?';
} else {
$className = $fullyQualifiedClassName;
$message = \sprintf('Attempted to load %s "%s" from the global namespace.', $typeName, $className);
$tail = '?';
}

if ($candidates = $this->getClassCandidates($className)) {
$tail = array_pop($candidates).'"?';
if ($candidates) {
$tail = ' for e.g. "'.implode('", "', $candidates).'" or "'.$tail;
} else {
$tail = ' for "'.$tail;
}
}
$message .= "\nDid you forget a \"use\" statement".$tail;

return new ClassNotFoundError($message, $error);
}











private function getClassCandidates(string $class): array
{
if (!\is_array($functions = spl_autoload_functions())) {
return [];
}


$classes = [];

foreach ($functions as $function) {
if (!\is_array($function)) {
continue;
}

if ($function[0] instanceof DebugClassLoader) {
$function = $function[0]->getClassLoader();

if (!\is_array($function)) {
continue;
}
}

if ($function[0] instanceof ClassLoader) {
foreach ($function[0]->getPrefixes() as $prefix => $paths) {
foreach ($paths as $path) {
$classes[] = $this->findClassInPath($path, $class, $prefix);
}
}

foreach ($function[0]->getPrefixesPsr4() as $prefix => $paths) {
foreach ($paths as $path) {
$classes[] = $this->findClassInPath($path, $class, $prefix);
}
}
}
}

return array_unique(array_merge([], ...$classes));
}

private function findClassInPath(string $path, string $class, string $prefix): array
{
$path = realpath($path.'/'.strtr($prefix, '\\_', '//')) ?: realpath($path.'/'.\dirname(strtr($prefix, '\\_', '//'))) ?: realpath($path);
if (!$path || !is_dir($path)) {
return [];
}

$classes = [];
$filename = $class.'.php';
foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
if ($filename == $file->getFileName() && $class = $this->convertFileToClass($path, $file->getPathName(), $prefix)) {
$classes[] = $class;
}
}

return $classes;
}

private function convertFileToClass(string $path, string $file, string $prefix): ?string
{
$candidates = [

$namespacedClass = str_replace([$path.\DIRECTORY_SEPARATOR, '.php', '/'], ['', '', '\\'], $file),

$prefix.$namespacedClass,

$prefix.'\\'.$namespacedClass,

str_replace('\\', '_', $namespacedClass),

str_replace('\\', '_', $prefix.$namespacedClass),

str_replace('\\', '_', $prefix.'\\'.$namespacedClass),
];

if ($prefix) {
$candidates = array_filter($candidates, fn ($candidate) => str_starts_with($candidate, $prefix));
}




foreach ($candidates as $candidate) {
if ($this->classExists($candidate)) {
return $candidate;
}
}





if (str_contains($file, 'Resources/stubs')) {
return null;
}

try {
require_once $file;
} catch (\Throwable) {
return null;
}

foreach ($candidates as $candidate) {
if ($this->classExists($candidate)) {
return $candidate;
}
}

return null;
}

private function classExists(string $class): bool
{
return class_exists($class, false) || interface_exists($class, false) || trait_exists($class, false);
}
}
