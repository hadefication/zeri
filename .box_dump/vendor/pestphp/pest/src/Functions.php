<?php

declare(strict_types=1);

use Pest\Concerns\Expectable;
use Pest\Configuration;
use Pest\Exceptions\AfterAllWithinDescribe;
use Pest\Exceptions\BeforeAllWithinDescribe;
use Pest\Expectation;
use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\PendingCalls\AfterEachCall;
use Pest\PendingCalls\BeforeEachCall;
use Pest\PendingCalls\DescribeCall;
use Pest\PendingCalls\TestCall;
use Pest\PendingCalls\UsesCall;
use Pest\Repositories\DatasetsRepository;
use Pest\Support\Backtrace;
use Pest\Support\Container;
use Pest\Support\DatasetInfo;
use Pest\Support\HigherOrderTapProxy;
use Pest\TestSuite;
use PHPUnit\Framework\TestCase;

if (! function_exists('expect')) {
/**
@template





*/
function expect(mixed $value = null): Expectation
{
return new Expectation($value);
}
}

if (! function_exists('beforeAll')) {



function beforeAll(Closure $closure): void
{
if (DescribeCall::describing() !== []) {
$filename = Backtrace::file();

throw new BeforeAllWithinDescribe($filename);
}

TestSuite::getInstance()->beforeAll->set($closure);
}
}

if (! function_exists('beforeEach')) {
/**
@param-closure-this




*/
function beforeEach(?Closure $closure = null): BeforeEachCall
{
$filename = Backtrace::file();

return new BeforeEachCall(TestSuite::getInstance(), $filename, $closure);
}
}

if (! function_exists('dataset')) {





function dataset(string $name, Closure|iterable $dataset): void
{
$scope = DatasetInfo::scope(Backtrace::datasetsFile());

DatasetsRepository::set($name, $dataset, $scope);
}
}

if (! function_exists('describe')) {







function describe(string $description, Closure $tests): DescribeCall
{
$filename = Backtrace::testFile();

return new DescribeCall(TestSuite::getInstance(), $filename, $description, $tests);
}
}

if (! function_exists('uses')) {






function uses(string ...$classAndTraits): UsesCall
{
$filename = Backtrace::file();

return new UsesCall($filename, array_values($classAndTraits));
}
}

if (! function_exists('pest')) {



function pest(): Configuration
{
return new Configuration(Backtrace::file());
}
}

if (! function_exists('test')) {
/**
@param-closure-this






*/
function test(?string $description = null, ?Closure $closure = null): HigherOrderTapProxy|TestCall
{
if ($description === null && TestSuite::getInstance()->test instanceof \PHPUnit\Framework\TestCase) {
return new HigherOrderTapProxy(TestSuite::getInstance()->test);
}

$filename = Backtrace::testFile();

return new TestCall(TestSuite::getInstance(), $filename, $description, $closure);
}
}

if (! function_exists('it')) {
/**
@param-closure-this






*/
function it(string $description, ?Closure $closure = null): TestCall
{
$description = sprintf('it %s', $description);


$test = test($description, $closure);

return $test;
}
}

if (! function_exists('todo')) {





function todo(string $description): TestCall
{
$test = test($description);

assert($test instanceof TestCall);

return $test->todo();
}
}

if (! function_exists('afterEach')) {
/**
@param-closure-this




*/
function afterEach(?Closure $closure = null): AfterEachCall
{
$filename = Backtrace::file();

return new AfterEachCall(TestSuite::getInstance(), $filename, $closure);
}
}

if (! function_exists('afterAll')) {



function afterAll(Closure $closure): void
{
if (DescribeCall::describing() !== []) {
$filename = Backtrace::file();

throw new AfterAllWithinDescribe($filename);
}

TestSuite::getInstance()->afterAll->set($closure);
}
}

if (! function_exists('covers')) {





function covers(array|string ...$classesOrFunctions): void
{
$filename = Backtrace::file();

$beforeEachCall = (new BeforeEachCall(TestSuite::getInstance(), $filename));

$beforeEachCall->covers(...$classesOrFunctions);
$beforeEachCall->group('__pest_mutate_only');


$runner = Container::getInstance()->get(MutationTestRunner::class);

$configurationRepository = Container::getInstance()->get(ConfigurationRepository::class);
$everything = $configurationRepository->cliConfiguration->toArray()['everything'] ?? false;
$classes = $configurationRepository->cliConfiguration->toArray()['classes'] ?? false;
$paths = $configurationRepository->cliConfiguration->toArray()['paths'] ?? false;

if ($runner->isEnabled() && ! $everything && ! is_array($classes) && ! is_array($paths)) {
$beforeEachCall->only('__pest_mutate_only');
}
}
}

if (! function_exists('mutates')) {





function mutates(array|string ...$targets): void
{
$filename = Backtrace::file();

$beforeEachCall = (new BeforeEachCall(TestSuite::getInstance(), $filename));
$beforeEachCall->group('__pest_mutate_only');


$runner = Container::getInstance()->get(MutationTestRunner::class);

$configurationRepository = Container::getInstance()->get(ConfigurationRepository::class);
$everything = $configurationRepository->cliConfiguration->toArray()['everything'] ?? false;
$classes = $configurationRepository->cliConfiguration->toArray()['classes'] ?? false;
$paths = $configurationRepository->cliConfiguration->toArray()['paths'] ?? false;

if ($runner->isEnabled() && ! $everything && ! is_array($classes) && ! is_array($paths)) {
$beforeEachCall->only('__pest_mutate_only');
}


$configurationRepository = Container::getInstance()->get(ConfigurationRepository::class);
$paths = $configurationRepository->cliConfiguration->toArray()['paths'] ?? false;

if (! is_array($paths)) {
$configurationRepository->globalConfiguration('default')->class(...$targets); 
}
}
}
