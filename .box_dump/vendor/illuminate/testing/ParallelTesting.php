<?php

namespace Illuminate\Testing;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;

class ParallelTesting
{





protected $container;






protected $optionsResolver;






protected $tokenResolver;






protected $setUpProcessCallbacks = [];






protected $setUpTestCaseCallbacks = [];






protected $setUpTestDatabaseCallbacks = [];






protected $tearDownProcessCallbacks = [];






protected $tearDownTestCaseCallbacks = [];






public function __construct(Container $container)
{
$this->container = $container;
}







public function resolveOptionsUsing($resolver)
{
$this->optionsResolver = $resolver;
}







public function resolveTokenUsing($resolver)
{
$this->tokenResolver = $resolver;
}







public function setUpProcess($callback)
{
$this->setUpProcessCallbacks[] = $callback;
}







public function setUpTestCase($callback)
{
$this->setUpTestCaseCallbacks[] = $callback;
}







public function setUpTestDatabase($callback)
{
$this->setUpTestDatabaseCallbacks[] = $callback;
}







public function tearDownProcess($callback)
{
$this->tearDownProcessCallbacks[] = $callback;
}







public function tearDownTestCase($callback)
{
$this->tearDownTestCaseCallbacks[] = $callback;
}






public function callSetUpProcessCallbacks()
{
$this->whenRunningInParallel(function () {
foreach ($this->setUpProcessCallbacks as $callback) {
$this->container->call($callback, [
'token' => $this->token(),
]);
}
});
}







public function callSetUpTestCaseCallbacks($testCase)
{
$this->whenRunningInParallel(function () use ($testCase) {
foreach ($this->setUpTestCaseCallbacks as $callback) {
$this->container->call($callback, [
'testCase' => $testCase,
'token' => $this->token(),
]);
}
});
}







public function callSetUpTestDatabaseCallbacks($database)
{
$this->whenRunningInParallel(function () use ($database) {
foreach ($this->setUpTestDatabaseCallbacks as $callback) {
$this->container->call($callback, [
'database' => $database,
'token' => $this->token(),
]);
}
});
}






public function callTearDownProcessCallbacks()
{
$this->whenRunningInParallel(function () {
foreach ($this->tearDownProcessCallbacks as $callback) {
$this->container->call($callback, [
'token' => $this->token(),
]);
}
});
}







public function callTearDownTestCaseCallbacks($testCase)
{
$this->whenRunningInParallel(function () use ($testCase) {
foreach ($this->tearDownTestCaseCallbacks as $callback) {
$this->container->call($callback, [
'testCase' => $testCase,
'token' => $this->token(),
]);
}
});
}







public function option($option)
{
$optionsResolver = $this->optionsResolver ?: function ($option) {
$option = 'LARAVEL_PARALLEL_TESTING_'.Str::upper($option);

return $_SERVER[$option] ?? false;
};

return $optionsResolver($option);
}






public function token()
{
return $this->tokenResolver
? call_user_func($this->tokenResolver)
: ($_SERVER['TEST_TOKEN'] ?? false);
}







protected function whenRunningInParallel($callback)
{
if ($this->inParallel()) {
$callback();
}
}






protected function inParallel()
{
return ! empty($_SERVER['LARAVEL_PARALLEL_TESTING']) && $this->token();
}
}
