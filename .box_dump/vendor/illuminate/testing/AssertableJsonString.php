<?php

namespace Illuminate\Testing;

use ArrayAccess;
use Closure;
use Countable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Testing\Assert as PHPUnit;
use JsonSerializable;

use function Illuminate\Support\enum_value;

class AssertableJsonString implements ArrayAccess, Countable
{





public $json;






protected $decoded;






public function __construct($jsonable)
{
$this->json = $jsonable;

if ($jsonable instanceof JsonSerializable) {
$this->decoded = $jsonable->jsonSerialize();
} elseif ($jsonable instanceof Jsonable) {
$this->decoded = json_decode($jsonable->toJson(), true);
} elseif (is_array($jsonable)) {
$this->decoded = $jsonable;
} else {
$this->decoded = json_decode($jsonable, true);
}
}







public function json($key = null)
{
return data_get($this->decoded, $key);
}








public function assertCount(int $count, $key = null)
{
if (! is_null($key)) {
PHPUnit::assertCount(
$count, data_get($this->decoded, $key),
"Failed to assert that the response count matched the expected {$count}"
);

return $this;
}

PHPUnit::assertCount($count,
$this->decoded,
"Failed to assert that the response count matched the expected {$count}"
);

return $this;
}







public function assertExact(array $data)
{
$actual = $this->reorderAssocKeys((array) $this->decoded);

$expected = $this->reorderAssocKeys($data);

PHPUnit::assertEquals(
json_encode($expected, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
json_encode($actual, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

return $this;
}







public function assertSimilar(array $data)
{
$actual = json_encode(
Arr::sortRecursive((array) $this->decoded),
JSON_UNESCAPED_UNICODE
);

PHPUnit::assertEquals(json_encode(Arr::sortRecursive($data), JSON_UNESCAPED_UNICODE), $actual);

return $this;
}







public function assertFragment(array $data)
{
$actual = json_encode(
Arr::sortRecursive((array) $this->decoded),
JSON_UNESCAPED_UNICODE
);

foreach (Arr::sortRecursive($data) as $key => $value) {
$expected = $this->jsonSearchStrings($key, $value);

PHPUnit::assertTrue(
Str::contains($actual, $expected),
'Unable to find JSON fragment: '.PHP_EOL.PHP_EOL.
'['.json_encode([$key => $value], JSON_UNESCAPED_UNICODE).']'.PHP_EOL.PHP_EOL.
'within'.PHP_EOL.PHP_EOL.
"[{$actual}]."
);
}

return $this;
}








public function assertMissing(array $data, $exact = false)
{
if ($exact) {
return $this->assertMissingExact($data);
}

$actual = json_encode(
Arr::sortRecursive((array) $this->decoded),
JSON_UNESCAPED_UNICODE
);

foreach (Arr::sortRecursive($data) as $key => $value) {
$unexpected = $this->jsonSearchStrings($key, $value);

PHPUnit::assertFalse(
Str::contains($actual, $unexpected),
'Found unexpected JSON fragment: '.PHP_EOL.PHP_EOL.
'['.json_encode([$key => $value], JSON_UNESCAPED_UNICODE).']'.PHP_EOL.PHP_EOL.
'within'.PHP_EOL.PHP_EOL.
"[{$actual}]."
);
}

return $this;
}







public function assertMissingExact(array $data)
{
$actual = json_encode(
Arr::sortRecursive((array) $this->decoded),
JSON_UNESCAPED_UNICODE
);

foreach (Arr::sortRecursive($data) as $key => $value) {
$unexpected = $this->jsonSearchStrings($key, $value);

if (! Str::contains($actual, $unexpected)) {
return $this;
}
}

PHPUnit::fail(
'Found unexpected JSON fragment: '.PHP_EOL.PHP_EOL.
'['.json_encode($data, JSON_UNESCAPED_UNICODE).']'.PHP_EOL.PHP_EOL.
'within'.PHP_EOL.PHP_EOL.
"[{$actual}]."
);
}







public function assertMissingPath($path)
{
PHPUnit::assertFalse(Arr::has($this->json(), $path));

return $this;
}








public function assertPath($path, $expect)
{
if ($expect instanceof Closure) {
PHPUnit::assertTrue($expect($this->json($path)));
} else {
PHPUnit::assertSame(enum_value($expect), $this->json($path));
}

return $this;
}








public function assertPathCanonicalizing($path, $expect)
{
PHPUnit::assertEqualsCanonicalizing($expect, $this->json($path));

return $this;
}









public function assertStructure(?array $structure = null, $responseData = null, bool $exact = false)
{
if (is_null($structure)) {
return $this->assertSimilar($this->decoded);
}

if (! is_null($responseData)) {
return (new static($responseData))->assertStructure($structure, null, $exact);
}

if ($exact) {
PHPUnit::assertIsArray($this->decoded);

$keys = (new Collection($structure))->map(fn ($value, $key) => is_array($value) ? $key : $value)->values();

if ($keys->all() !== ['*']) {
PHPUnit::assertEquals($keys->sort()->values()->all(), (new Collection($this->decoded))->keys()->sort()->values()->all());
}
}

foreach ($structure as $key => $value) {
if (is_array($value) && $key === '*') {
PHPUnit::assertIsArray($this->decoded);

foreach ($this->decoded as $responseDataItem) {
$this->assertStructure($structure['*'], $responseDataItem, $exact);
}
} elseif (is_array($value)) {
PHPUnit::assertArrayHasKey($key, $this->decoded);

$this->assertStructure($structure[$key], $this->decoded[$key], $exact);
} else {
PHPUnit::assertArrayHasKey($value, $this->decoded);
}
}

return $this;
}








public function assertSubset(array $data, $strict = false)
{
PHPUnit::assertArraySubset(
$data, $this->decoded, $strict, $this->assertJsonMessage($data)
);

return $this;
}







protected function reorderAssocKeys(array $data)
{
$data = Arr::dot($data);
ksort($data);

$result = [];

foreach ($data as $key => $value) {
Arr::set($result, $key, $value);
}

return $result;
}







protected function assertJsonMessage(array $data)
{
$expected = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$actual = json_encode($this->decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

return 'Unable to find JSON: '.PHP_EOL.PHP_EOL.
"[{$expected}]".PHP_EOL.PHP_EOL.
'within response JSON:'.PHP_EOL.PHP_EOL.
"[{$actual}].".PHP_EOL.PHP_EOL;
}








protected function jsonSearchStrings($key, $value)
{
$needle = Str::substr(json_encode([$key => $value], JSON_UNESCAPED_UNICODE), 1, -1);

return [
$needle.']',
$needle.'}',
$needle.',',
];
}






public function count(): int
{
return count($this->decoded);
}







public function offsetExists($offset): bool
{
return isset($this->decoded[$offset]);
}







public function offsetGet($offset): mixed
{
return $this->decoded[$offset];
}








public function offsetSet($offset, $value): void
{
$this->decoded[$offset] = $value;
}







public function offsetUnset($offset): void
{
unset($this->decoded[$offset]);
}
}
