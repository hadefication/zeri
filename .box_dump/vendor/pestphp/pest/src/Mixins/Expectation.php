<?php

declare(strict_types=1);

namespace Pest\Mixins;

use BadMethodCallException;
use Closure;
use Countable;
use DateTimeInterface;
use Error;
use InvalidArgumentException;
use JsonSerializable;
use Pest\Exceptions\InvalidExpectationValue;
use Pest\Matchers\Any;
use Pest\Support\Arr;
use Pest\Support\Exporter;
use Pest\Support\NullClosure;
use Pest\Support\Str;
use Pest\TestSuite;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use ReflectionFunction;
use ReflectionNamedType;
use Throwable;
use Traversable;

/**
@template
@mixin



*/
final class Expectation
{



private ?Exporter $exporter = null;






public function __construct(
public mixed $value
) {

}








public function toBe(mixed $expected, string $message = ''): self
{
Assert::assertSame($expected, $this->value, $message);

return $this;
}






public function toBeEmpty(string $message = ''): self
{
Assert::assertEmpty($this->value, $message);

return $this;
}






public function toBeTrue(string $message = ''): self
{
Assert::assertTrue($this->value, $message);

return $this;
}






public function toBeTruthy(string $message = ''): self
{
Assert::assertTrue((bool) $this->value, $message);

return $this;
}






public function toBeFalse(string $message = ''): self
{
Assert::assertFalse($this->value, $message);

return $this;
}






public function toBeFalsy(string $message = ''): self
{
Assert::assertFalse((bool) $this->value, $message);

return $this;
}






public function toBeGreaterThan(int|float|string|DateTimeInterface $expected, string $message = ''): self
{
Assert::assertGreaterThan($expected, $this->value, $message);

return $this;
}






public function toBeGreaterThanOrEqual(int|float|string|DateTimeInterface $expected, string $message = ''): self
{
Assert::assertGreaterThanOrEqual($expected, $this->value, $message);

return $this;
}






public function toBeLessThan(int|float|string|DateTimeInterface $expected, string $message = ''): self
{
Assert::assertLessThan($expected, $this->value, $message);

return $this;
}






public function toBeLessThanOrEqual(int|float|string|DateTimeInterface $expected, string $message = ''): self
{
Assert::assertLessThanOrEqual($expected, $this->value, $message);

return $this;
}






public function toContain(mixed ...$needles): self
{
foreach ($needles as $needle) {
if (is_string($this->value)) {
Assert::assertStringContainsString((string) $needle, $this->value);
} else {
if (! is_iterable($this->value)) {
InvalidExpectationValue::expected('iterable');
}
Assert::assertContains($needle, $this->value);
}
}

return $this;
}






public function toContainEqual(mixed ...$needles): self
{
if (! is_iterable($this->value)) {
InvalidExpectationValue::expected('iterable');
}

foreach ($needles as $needle) {
Assert::assertContainsEquals($needle, $this->value);
}

return $this;
}







public function toStartWith(string $expected, string $message = ''): self
{
if (! is_string($this->value)) {
InvalidExpectationValue::expected('string');
}

Assert::assertStringStartsWith($expected, $this->value, $message);

return $this;
}







public function toEndWith(string $expected, string $message = ''): self
{
if (! is_string($this->value)) {
InvalidExpectationValue::expected('string');
}

Assert::assertStringEndsWith($expected, $this->value, $message);

return $this;
}






public function toHaveLength(int $number, string $message = ''): self
{
if (is_string($this->value)) {
Assert::assertEquals($number, mb_strlen($this->value), $message);

return $this;
}

if (is_iterable($this->value)) {
return $this->toHaveCount($number, $message);
}

if (is_object($this->value)) {
$array = method_exists($this->value, 'toArray') ? $this->value->toArray() : (array) $this->value;

Assert::assertCount($number, $array, $message);

return $this;
}

throw new BadMethodCallException('Expectation value length is not countable.');
}






public function toHaveCount(int $count, string $message = ''): self
{
if (! is_countable($this->value) && ! is_iterable($this->value)) {
InvalidExpectationValue::expected('countable|iterable');
}

Assert::assertCount($count, $this->value, $message);

return $this;
}







public function toHaveSameSize(Countable|iterable $expected, string $message = ''): self
{
if (! is_countable($this->value) && ! is_iterable($this->value)) {
InvalidExpectationValue::expected('countable|iterable');
}

Assert::assertSameSize($expected, $this->value, $message);

return $this;
}






public function toHaveProperty(string $name, mixed $value = new Any, string $message = ''): self
{
$this->toBeObject();


Assert::assertTrue(property_exists($this->value, $name), $message);

if (! $value instanceof Any) {

Assert::assertEquals($value, $this->value->{$name}, $message);
}

return $this;
}







public function toHaveProperties(iterable $names, string $message = ''): self
{
foreach ($names as $name => $value) {
is_int($name) ? $this->toHaveProperty($value, message: $message) : $this->toHaveProperty($name, $value, $message); 
}

return $this;
}






public function toEqual(mixed $expected, string $message = ''): self
{
Assert::assertEquals($expected, $this->value, $message);

return $this;
}












public function toEqualCanonicalizing(mixed $expected, string $message = ''): self
{
Assert::assertEqualsCanonicalizing($expected, $this->value, $message);

return $this;
}







public function toEqualWithDelta(mixed $expected, float $delta, string $message = ''): self
{
Assert::assertEqualsWithDelta($expected, $this->value, $delta, $message);

return $this;
}







public function toBeIn(iterable $values, string $message = ''): self
{
Assert::assertContains($this->value, $values, $message);

return $this;
}






public function toBeInfinite(string $message = ''): self
{
Assert::assertInfinite($this->value, $message);

return $this;
}







public function toBeInstanceOf(string $class, string $message = ''): self
{
Assert::assertInstanceOf($class, $this->value, $message);

return $this;
}






public function toBeArray(string $message = ''): self
{
Assert::assertIsArray($this->value, $message);

return $this;
}






public function toBeList(string $message = ''): self
{
Assert::assertIsList($this->value, $message);

return $this;
}






public function toBeBool(string $message = ''): self
{
Assert::assertIsBool($this->value, $message);

return $this;
}






public function toBeCallable(string $message = ''): self
{
Assert::assertIsCallable($this->value, $message);

return $this;
}






public function toBeFloat(string $message = ''): self
{
Assert::assertIsFloat($this->value, $message);

return $this;
}






public function toBeInt(string $message = ''): self
{
Assert::assertIsInt($this->value, $message);

return $this;
}






public function toBeIterable(string $message = ''): self
{
Assert::assertIsIterable($this->value, $message);

return $this;
}






public function toBeNumeric(string $message = ''): self
{
Assert::assertIsNumeric($this->value, $message);

return $this;
}






public function toBeDigits(string $message = ''): self
{
Assert::assertTrue(ctype_digit((string) $this->value), $message);

return $this;
}






public function toBeObject(string $message = ''): self
{
Assert::assertIsObject($this->value, $message);

return $this;
}






public function toBeResource(string $message = ''): self
{
Assert::assertIsResource($this->value, $message);

return $this;
}






public function toBeScalar(string $message = ''): self
{
Assert::assertIsScalar($this->value, $message);

return $this;
}






public function toBeString(string $message = ''): self
{
Assert::assertIsString($this->value, $message);

return $this;
}






public function toBeJson(string $message = ''): self
{
Assert::assertIsString($this->value, $message);

Assert::assertJson($this->value, $message);

return $this;
}






public function toBeNan(string $message = ''): self
{
Assert::assertNan($this->value, $message);

return $this;
}






public function toBeNull(string $message = ''): self
{
Assert::assertNull($this->value, $message);

return $this;
}






public function toHaveKey(string|int $key, mixed $value = new Any, string $message = ''): self
{
if (is_object($this->value) && method_exists($this->value, 'toArray')) {
$array = $this->value->toArray();
} else {
$array = (array) $this->value;
}

try {
Assert::assertTrue(Arr::has($array, $key));


} catch (ExpectationFailedException $exception) {
if ($message === '') {
$message = "Failed asserting that an array has the key '$key'";
}

throw new ExpectationFailedException($message, $exception->getComparisonFailure());
}

if (! $value instanceof Any) {
Assert::assertEquals($value, Arr::get($array, $key), $message);
}

return $this;
}







public function toHaveKeys(array $keys, string $message = ''): self
{
foreach ($keys as $k => $key) {
if (is_array($key)) {
$this->toHaveKeys(array_keys(Arr::dot($key, $k.'.')), $message);
} else {
$this->toHaveKey($key, message: $message);
}
}

return $this;
}






public function toBeDirectory(string $message = ''): self
{
if (! is_string($this->value)) {
InvalidExpectationValue::expected('string');
}

Assert::assertDirectoryExists($this->value, $message);

return $this;
}






public function toBeReadableDirectory(string $message = ''): self
{
if (! is_string($this->value)) {
InvalidExpectationValue::expected('string');
}

Assert::assertDirectoryIsReadable($this->value, $message);

return $this;
}






public function toBeWritableDirectory(string $message = ''): self
{
if (! is_string($this->value)) {
InvalidExpectationValue::expected('string');
}

Assert::assertDirectoryIsWritable($this->value, $message);

return $this;
}






public function toBeFile(string $message = ''): self
{
if (! is_string($this->value)) {
InvalidExpectationValue::expected('string');
}

Assert::assertFileExists($this->value, $message);

return $this;
}






public function toBeReadableFile(string $message = ''): self
{
if (! is_string($this->value)) {
InvalidExpectationValue::expected('string');
}

Assert::assertFileIsReadable($this->value, $message);

return $this;
}






public function toBeWritableFile(string $message = ''): self
{
if (! is_string($this->value)) {
InvalidExpectationValue::expected('string');
}
Assert::assertFileIsWritable($this->value, $message);

return $this;
}







public function toMatchArray(iterable $array, string $message = ''): self
{
if (is_object($this->value) && method_exists($this->value, 'toArray')) {
$valueAsArray = $this->value->toArray();
} else {
$valueAsArray = (array) $this->value;
}

foreach ($array as $key => $value) {
Assert::assertArrayHasKey($key, $valueAsArray, $message);

if ($message === '') {
$message = sprintf(
'Failed asserting that an array has a key %s with the value %s.',
$this->export($key),
$this->export($valueAsArray[$key]),
);
}

Assert::assertEquals($value, $valueAsArray[$key], $message);
}

return $this;
}








public function toMatchObject(iterable $object, string $message = ''): self
{
foreach ((array) $object as $property => $value) {
if (! is_object($this->value) && ! is_string($this->value)) {
InvalidExpectationValue::expected('object|string');
}

Assert::assertTrue(property_exists($this->value, $property), $message);


$propertyValue = $this->value->{$property};

if ($message === '') {
$message = sprintf(
'Failed asserting that an object has a property %s with the value %s.',
$this->export($property),
$this->export($propertyValue),
);
}

Assert::assertEquals($value, $propertyValue, $message);
}

return $this;
}






public function toMatchSnapshot(string $message = ''): self
{
$snapshots = TestSuite::getInstance()->snapshots;
$snapshots->startNewExpectation();

$testCase = TestSuite::getInstance()->test;
assert($testCase instanceof TestCase);

$string = match (true) {
is_string($this->value) => $this->value,
is_object($this->value) && method_exists($this->value, 'toSnapshot') => $this->value->toSnapshot(),
is_object($this->value) && method_exists($this->value, '__toString') => $this->value->__toString(),
is_object($this->value) && method_exists($this->value, 'toString') => $this->value->toString(),
$this->value instanceof \Illuminate\Testing\TestResponse => $this->value->getContent(), 
is_array($this->value) => json_encode($this->value, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
$this->value instanceof Traversable => json_encode(iterator_to_array($this->value), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
$this->value instanceof JsonSerializable => json_encode($this->value->jsonSerialize(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
is_object($this->value) && method_exists($this->value, 'toArray') => json_encode($this->value->toArray(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
default => InvalidExpectationValue::expected('array|object|string'),
};

if ($snapshots->has()) {
[$filename, $content] = $snapshots->get();

Assert::assertSame(
strtr($content, ["\r\n" => "\n", "\r" => "\n"]),
strtr($string, ["\r\n" => "\n", "\r" => "\n"]),
$message === '' ? "Failed asserting that the string value matches its snapshot ($filename)." : $message
);
} else {
$filename = $snapshots->save($string);

TestSuite::getInstance()->registerSnapshotChange("Snapshot created at [$filename]");
}

return $this;
}






public function toMatch(string $expression, string $message = ''): self
{
if (! is_string($this->value)) {
InvalidExpectationValue::expected('string');
}
Assert::assertMatchesRegularExpression($expression, $this->value, $message);

return $this;
}






public function toMatchConstraint(Constraint $constraint, string $message = ''): self
{
Assert::assertThat($this->value, $constraint, $message);

return $this;
}





public function toContainOnlyInstancesOf(string $class, string $message = ''): self
{
if (! is_iterable($this->value)) {
InvalidExpectationValue::expected('iterable');
}

Assert::assertContainsOnlyInstancesOf($class, $this->value, $message);

return $this;
}







public function toThrow(callable|string|Throwable $exception, ?string $exceptionMessage = null, string $message = ''): self
{
$callback = NullClosure::create();

if ($exception instanceof Closure) {
$callback = $exception;
$parameters = (new ReflectionFunction($exception))->getParameters();

if (count($parameters) !== 1) {
throw new InvalidArgumentException('The given closure must have a single parameter type-hinted as the class string.');
}

if (! ($type = $parameters[0]->getType()) instanceof ReflectionNamedType) {
throw new InvalidArgumentException('The given closure\'s parameter must be type-hinted as the class string.');
}

$exception = $type->getName();
}

try {
($this->value)();
} catch (Throwable $e) {

if ($exception instanceof Throwable) {
expect($e)
->toBeInstanceOf($exception::class, $message)
->and($e->getMessage())->toBe($exceptionMessage ?? $exception->getMessage(), $message);

return $this;
}

if (! class_exists($exception)) {
if ($e instanceof Error && "Class \"$exception\" not found" === $e->getMessage()) {
Assert::assertTrue(true);

throw $e;
}

Assert::assertStringContainsString($exception, $e->getMessage(), $message);

return $this;
}

if ($exceptionMessage !== null) {
Assert::assertStringContainsString($exceptionMessage, $e->getMessage(), $message);
}

Assert::assertInstanceOf($exception, $e, $message);

$callback($e);

return $this;
}

Assert::assertTrue(true);

if (! $exception instanceof Throwable && ! class_exists($exception)) {
throw new ExpectationFailedException("Exception with message \"$exception\" not thrown.");
}

throw new ExpectationFailedException("Exception \"$exception\" not thrown.");
}




private function export(mixed $value): string
{
if (! $this->exporter instanceof \Pest\Support\Exporter) {
$this->exporter = Exporter::default();
}

return $this->exporter->shortenedExport($value);
}






public function toBeUppercase(string $message = ''): self
{
Assert::assertTrue(ctype_upper((string) $this->value), $message);

return $this;
}






public function toBeLowercase(string $message = ''): self
{
Assert::assertTrue(ctype_lower((string) $this->value), $message);

return $this;
}






public function toBeAlphaNumeric(string $message = ''): self
{
Assert::assertTrue(ctype_alnum((string) $this->value), $message);

return $this;
}






public function toBeAlpha(string $message = ''): self
{
Assert::assertTrue(ctype_alpha((string) $this->value), $message);

return $this;
}






public function toBeSnakeCase(string $message = ''): self
{
$value = (string) $this->value;

if ($message === '') {
$message = "Failed asserting that {$value} is snake_case.";
}

Assert::assertTrue((bool) preg_match('/^[\p{Ll}_]+$/u', $value), $message);

return $this;
}






public function toBeKebabCase(string $message = ''): self
{
$value = (string) $this->value;

if ($message === '') {
$message = "Failed asserting that {$value} is kebab-case.";
}

Assert::assertTrue((bool) preg_match('/^[\p{Ll}-]+$/u', $value), $message);

return $this;
}






public function toBeCamelCase(string $message = ''): self
{
$value = (string) $this->value;

if ($message === '') {
$message = "Failed asserting that {$value} is camelCase.";
}

Assert::assertTrue((bool) preg_match('/^\p{Ll}[\p{Ll}\p{Lu}]+$/u', $value), $message);

return $this;
}






public function toBeStudlyCase(string $message = ''): self
{
$value = (string) $this->value;

if ($message === '') {
$message = "Failed asserting that {$value} is StudlyCase.";
}

Assert::assertTrue((bool) preg_match('/^\p{Lu}+\p{Ll}[\p{Ll}\p{Lu}]+$/u', $value), $message);

return $this;
}






public function toBeUuid(string $message = ''): self
{
if (! is_string($this->value)) {
InvalidExpectationValue::expected('string');
}

Assert::assertTrue(Str::isUuid($this->value), $message);

return $this;
}






public function toBeBetween(int|float|DateTimeInterface $lowestValue, int|float|DateTimeInterface $highestValue, string $message = ''): self
{
Assert::assertGreaterThanOrEqual($lowestValue, $this->value, $message);
Assert::assertLessThanOrEqual($highestValue, $this->value, $message);

return $this;
}






public function toBeUrl(string $message = ''): self
{
if ($message === '') {
$message = "Failed asserting that {$this->value} is a url.";
}

Assert::assertTrue(Str::isUrl((string) $this->value), $message);

return $this;
}
}
