<?php

declare(strict_types=1);

namespace Pest\Repositories;

use Closure;
use Pest\Contracts\TestCaseFilter;
use Pest\Contracts\TestCaseMethodFilter;
use Pest\Exceptions\TestCaseAlreadyInUse;
use Pest\Exceptions\TestCaseClassOrTraitNotFound;
use Pest\Factories\Attribute;
use Pest\Factories\TestCaseFactory;
use Pest\Factories\TestCaseMethodFactory;
use Pest\Support\Str;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;




final class TestRepository
{



private array $testCases = [];




private array $uses = [];




private array $testCaseFilters = [];




private array $testCaseMethodFilters = [];




public function count(): int
{
return count($this->testCases);
}






public function getFilenames(): array
{
return array_values(array_map(static fn (TestCaseFactory $factory): string => $factory->filename, $this->testCases));
}









public function use(array $classOrTraits, array $groups, array $paths, array $hooks): void
{
foreach ($classOrTraits as $classOrTrait) {
if (class_exists($classOrTrait)) {
continue;
}
if (trait_exists($classOrTrait)) {
continue;
}
throw new TestCaseClassOrTraitNotFound($classOrTrait);
}

$hooks = array_map(fn (Closure $hook): array => [$hook], $hooks);

foreach ($paths as $path) {
if (array_key_exists($path, $this->uses)) {
$this->uses[$path] = [
[...$this->uses[$path][0], ...$classOrTraits],
[...$this->uses[$path][1], ...$groups],
array_map(
fn (int $index): array => [...$this->uses[$path][2][$index] ?? [], ...($hooks[$index] ?? [])],
range(0, 3),
),
];
} else {
$this->uses[$path] = [$classOrTraits, $groups, $hooks];
}
}
}




public function addTestCaseFilter(TestCaseFilter $filter): void
{
$this->testCaseFilters[] = $filter;
}




public function addTestCaseMethodFilter(TestCaseMethodFilter $filter): void
{
$this->testCaseMethodFilters[] = $filter;
}




public function get(string $filename): ?TestCaseFactory
{
return $this->testCases[$filename] ?? null;
}




public function set(TestCaseMethodFactory $method): void
{
foreach ($this->testCaseFilters as $filter) {
if (! $filter->accept($method->filename)) {
return;
}
}

foreach ($this->testCaseMethodFilters as $filter) {
if (! $filter->accept($method)) {
return;
}
}

if (! array_key_exists($method->filename, $this->testCases)) {
$this->testCases[$method->filename] = new TestCaseFactory($method->filename);
}

$this->testCases[$method->filename]->addMethod($method);
}




public function makeIfNeeded(string $filename): void
{
if (! array_key_exists($filename, $this->testCases)) {
return;
}

foreach ($this->testCaseFilters as $filter) {
if (! $filter->accept($filename)) {
return;
}
}

$this->make($this->testCases[$filename]);
}




private function make(TestCaseFactory $testCase): void
{
$startsWith = static fn (string $target, string $directory): bool => Str::startsWith($target, $directory.DIRECTORY_SEPARATOR);

foreach ($this->uses as $path => $uses) {
[$classOrTraits, $groups, $hooks] = $uses;

if ((! is_dir($path) && $testCase->filename === $path) || (is_dir($path) && $startsWith($testCase->filename, $path))) {
foreach ($classOrTraits as $class) {

if (class_exists($class)) {
if ($testCase->class !== TestCase::class) {
throw new TestCaseAlreadyInUse($testCase->class, $class, $testCase->filename);
}
$testCase->class = $class;
} elseif (trait_exists($class)) {
$testCase->traits[] = $class;
}
}

foreach ($testCase->methods as $method) {
foreach ($groups as $group) {
$method->attributes[] = new Attribute(
Group::class,
[$group],
);
}
}

foreach ($testCase->methods as $method) {
$method->groups = [...$groups, ...$method->groups];
}

foreach (['__addBeforeAll', '__addBeforeEach', '__addAfterEach', '__addAfterAll'] as $index => $name) {
foreach ($hooks[$index] ?? [null] as $hook) {
$testCase->factoryProxies->add($testCase->filename, 0, $name, [$hook]);
}
}
}
}

$testCase->make();
}
}
