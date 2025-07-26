<?php declare(strict_types=1);








namespace PHPUnit\Framework;

use function func_get_args;
use function function_exists;
use ArrayAccess;
use Countable;
use PHPUnit\Framework\Constraint\ArrayHasKey;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\Constraint\Count;
use PHPUnit\Framework\Constraint\DirectoryExists;
use PHPUnit\Framework\Constraint\FileExists;
use PHPUnit\Framework\Constraint\GreaterThan;
use PHPUnit\Framework\Constraint\IsAnything;
use PHPUnit\Framework\Constraint\IsEmpty;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsEqualCanonicalizing;
use PHPUnit\Framework\Constraint\IsEqualIgnoringCase;
use PHPUnit\Framework\Constraint\IsEqualWithDelta;
use PHPUnit\Framework\Constraint\IsFalse;
use PHPUnit\Framework\Constraint\IsFinite;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\Constraint\IsInfinite;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\Constraint\IsJson;
use PHPUnit\Framework\Constraint\IsList;
use PHPUnit\Framework\Constraint\IsNan;
use PHPUnit\Framework\Constraint\IsNull;
use PHPUnit\Framework\Constraint\IsReadable;
use PHPUnit\Framework\Constraint\IsTrue;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\Constraint\IsWritable;
use PHPUnit\Framework\Constraint\LessThan;
use PHPUnit\Framework\Constraint\LogicalAnd;
use PHPUnit\Framework\Constraint\LogicalNot;
use PHPUnit\Framework\Constraint\LogicalOr;
use PHPUnit\Framework\Constraint\LogicalXor;
use PHPUnit\Framework\Constraint\ObjectEquals;
use PHPUnit\Framework\Constraint\RegularExpression;
use PHPUnit\Framework\Constraint\StringContains;
use PHPUnit\Framework\Constraint\StringEndsWith;
use PHPUnit\Framework\Constraint\StringEqualsStringIgnoringLineEndings;
use PHPUnit\Framework\Constraint\StringMatchesFormatDescription;
use PHPUnit\Framework\Constraint\StringStartsWith;
use PHPUnit\Framework\Constraint\TraversableContainsEqual;
use PHPUnit\Framework\Constraint\TraversableContainsIdentical;
use PHPUnit\Framework\Constraint\TraversableContainsOnly;
use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount as AnyInvokedCountMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtLeastCount as InvokedAtLeastCountMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtLeastOnce as InvokedAtLeastOnceMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedAtMostCount as InvokedAtMostCountMatcher;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls as ConsecutiveCallsStub;
use PHPUnit\Framework\MockObject\Stub\Exception as ExceptionStub;
use PHPUnit\Framework\MockObject\Stub\ReturnArgument as ReturnArgumentStub;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback as ReturnCallbackStub;
use PHPUnit\Framework\MockObject\Stub\ReturnSelf as ReturnSelfStub;
use PHPUnit\Framework\MockObject\Stub\ReturnStub;
use PHPUnit\Framework\MockObject\Stub\ReturnValueMap as ReturnValueMapStub;
use PHPUnit\Util\Xml\XmlException;
use Throwable;

if (!function_exists('PHPUnit\Framework\assertArrayIsEqualToArrayOnlyConsideringListOfKeys')) {
/**
@no-named-arguments











*/
function assertArrayIsEqualToArrayOnlyConsideringListOfKeys(array $expected, array $actual, array $keysToBeConsidered, string $message = ''): void
{
Assert::assertArrayIsEqualToArrayOnlyConsideringListOfKeys(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertArrayIsEqualToArrayIgnoringListOfKeys')) {
/**
@no-named-arguments











*/
function assertArrayIsEqualToArrayIgnoringListOfKeys(array $expected, array $actual, array $keysToBeIgnored, string $message = ''): void
{
Assert::assertArrayIsEqualToArrayIgnoringListOfKeys(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertArrayIsIdenticalToArrayOnlyConsideringListOfKeys')) {
/**
@no-named-arguments











*/
function assertArrayIsIdenticalToArrayOnlyConsideringListOfKeys(array $expected, array $actual, array $keysToBeConsidered, string $message = ''): void
{
Assert::assertArrayIsIdenticalToArrayOnlyConsideringListOfKeys(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertArrayIsIdenticalToArrayIgnoringListOfKeys')) {
/**
@no-named-arguments











*/
function assertArrayIsIdenticalToArrayIgnoringListOfKeys(array $expected, array $actual, array $keysToBeIgnored, string $message = ''): void
{
Assert::assertArrayIsIdenticalToArrayIgnoringListOfKeys(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertArrayHasKey')) {
/**
@no-named-arguments









*/
function assertArrayHasKey(mixed $key, array|ArrayAccess $array, string $message = ''): void
{
Assert::assertArrayHasKey(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertArrayNotHasKey')) {
/**
@no-named-arguments









*/
function assertArrayNotHasKey(mixed $key, array|ArrayAccess $array, string $message = ''): void
{
Assert::assertArrayNotHasKey(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsList')) {
/**
@phpstan-assert
@no-named-arguments





*/
function assertIsList(mixed $array, string $message = ''): void
{
Assert::assertIsList(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContains')) {
/**
@no-named-arguments









*/
function assertContains(mixed $needle, iterable $haystack, string $message = ''): void
{
Assert::assertContains(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsEquals')) {
/**
@no-named-arguments






*/
function assertContainsEquals(mixed $needle, iterable $haystack, string $message = ''): void
{
Assert::assertContainsEquals(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotContains')) {
/**
@no-named-arguments









*/
function assertNotContains(mixed $needle, iterable $haystack, string $message = ''): void
{
Assert::assertNotContains(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotContainsEquals')) {
/**
@no-named-arguments






*/
function assertNotContainsEquals(mixed $needle, iterable $haystack, string $message = ''): void
{
Assert::assertNotContainsEquals(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnly')) {
/**
@no-named-arguments












*/
function assertContainsOnly(string $type, iterable $haystack, ?bool $isNativeType = null, string $message = ''): void
{
Assert::assertContainsOnly(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyArray')) {
/**
@no-named-arguments








*/
function assertContainsOnlyArray(iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyArray(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyBool')) {
/**
@no-named-arguments








*/
function assertContainsOnlyBool(iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyBool(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyCallable')) {
/**
@no-named-arguments








*/
function assertContainsOnlyCallable(iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyCallable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyFloat')) {
/**
@no-named-arguments








*/
function assertContainsOnlyFloat(iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyFloat(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyInt')) {
/**
@no-named-arguments








*/
function assertContainsOnlyInt(iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyInt(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyIterable')) {
/**
@no-named-arguments








*/
function assertContainsOnlyIterable(iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyIterable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyNull')) {
/**
@no-named-arguments








*/
function assertContainsOnlyNull(iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyNull(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyNumeric')) {
/**
@no-named-arguments








*/
function assertContainsOnlyNumeric(iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyNumeric(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyObject')) {
/**
@no-named-arguments








*/
function assertContainsOnlyObject(iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyObject(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyResource')) {
/**
@no-named-arguments








*/
function assertContainsOnlyResource(iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyResource(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyClosedResource')) {
/**
@no-named-arguments








*/
function assertContainsOnlyClosedResource(iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyClosedResource(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyScalar')) {
/**
@no-named-arguments








*/
function assertContainsOnlyScalar(iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyScalar(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyString')) {
/**
@no-named-arguments








*/
function assertContainsOnlyString(iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyString(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsOnlyInstancesOf')) {
/**
@no-named-arguments










*/
function assertContainsOnlyInstancesOf(string $className, iterable $haystack, string $message = ''): void
{
Assert::assertContainsOnlyInstancesOf(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotContainsOnly')) {
/**
@no-named-arguments












*/
function assertNotContainsOnly(string $type, iterable $haystack, ?bool $isNativeType = null, string $message = ''): void
{
Assert::assertNotContainsOnly(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyArray')) {
/**
@no-named-arguments








*/
function assertContainsNotOnlyArray(iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyArray(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyBool')) {
/**
@no-named-arguments








*/
function assertContainsNotOnlyBool(iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyBool(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyCallable')) {
/**
@no-named-arguments








*/
function assertContainsNotOnlyCallable(iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyCallable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyFloat')) {
/**
@no-named-arguments








*/
function assertContainsNotOnlyFloat(iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyFloat(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyInt')) {
/**
@no-named-arguments








*/
function assertContainsNotOnlyInt(iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyInt(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyIterable')) {
/**
@no-named-arguments








*/
function assertContainsNotOnlyIterable(iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyIterable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyNull')) {
/**
@no-named-arguments








*/
function assertContainsNotOnlyNull(iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyNull(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyNumeric')) {
/**
@no-named-arguments








*/
function assertContainsNotOnlyNumeric(iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyNumeric(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyObject')) {
/**
@no-named-arguments








*/
function assertContainsNotOnlyObject(iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyObject(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyResource')) {
/**
@no-named-arguments








*/
function assertContainsNotOnlyResource(iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyResource(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyClosedResource')) {
/**
@no-named-arguments








*/
function assertContainsNotOnlyClosedResource(iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyClosedResource(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyScalar')) {
/**
@no-named-arguments








*/
function assertContainsNotOnlyScalar(iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyScalar(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyString')) {
/**
@no-named-arguments








*/
function assertContainsNotOnlyString(iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyString(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertContainsNotOnlyInstancesOf')) {
/**
@no-named-arguments










*/
function assertContainsNotOnlyInstancesOf(string $className, iterable $haystack, string $message = ''): void
{
Assert::assertContainsNotOnlyInstancesOf(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertCount')) {
/**
@no-named-arguments










*/
function assertCount(int $expectedCount, Countable|iterable $haystack, string $message = ''): void
{
Assert::assertCount(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotCount')) {
/**
@no-named-arguments










*/
function assertNotCount(int $expectedCount, Countable|iterable $haystack, string $message = ''): void
{
Assert::assertNotCount(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertEquals')) {
/**
@no-named-arguments






*/
function assertEquals(mixed $expected, mixed $actual, string $message = ''): void
{
Assert::assertEquals(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertEqualsCanonicalizing')) {
/**
@no-named-arguments






*/
function assertEqualsCanonicalizing(mixed $expected, mixed $actual, string $message = ''): void
{
Assert::assertEqualsCanonicalizing(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertEqualsIgnoringCase')) {
/**
@no-named-arguments






*/
function assertEqualsIgnoringCase(mixed $expected, mixed $actual, string $message = ''): void
{
Assert::assertEqualsIgnoringCase(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertEqualsWithDelta')) {
/**
@no-named-arguments






*/
function assertEqualsWithDelta(mixed $expected, mixed $actual, float $delta, string $message = ''): void
{
Assert::assertEqualsWithDelta(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotEquals')) {
/**
@no-named-arguments






*/
function assertNotEquals(mixed $expected, mixed $actual, string $message = ''): void
{
Assert::assertNotEquals(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotEqualsCanonicalizing')) {
/**
@no-named-arguments






*/
function assertNotEqualsCanonicalizing(mixed $expected, mixed $actual, string $message = ''): void
{
Assert::assertNotEqualsCanonicalizing(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotEqualsIgnoringCase')) {
/**
@no-named-arguments






*/
function assertNotEqualsIgnoringCase(mixed $expected, mixed $actual, string $message = ''): void
{
Assert::assertNotEqualsIgnoringCase(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotEqualsWithDelta')) {
/**
@no-named-arguments






*/
function assertNotEqualsWithDelta(mixed $expected, mixed $actual, float $delta, string $message = ''): void
{
Assert::assertNotEqualsWithDelta(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertObjectEquals')) {
/**
@no-named-arguments




*/
function assertObjectEquals(object $expected, object $actual, string $method = 'equals', string $message = ''): void
{
Assert::assertObjectEquals(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertObjectNotEquals')) {
/**
@no-named-arguments




*/
function assertObjectNotEquals(object $expected, object $actual, string $method = 'equals', string $message = ''): void
{
Assert::assertObjectNotEquals(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertEmpty')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertEmpty(mixed $actual, string $message = ''): void
{
Assert::assertEmpty(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotEmpty')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertNotEmpty(mixed $actual, string $message = ''): void
{
Assert::assertNotEmpty(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertGreaterThan')) {
/**
@no-named-arguments






*/
function assertGreaterThan(mixed $minimum, mixed $actual, string $message = ''): void
{
Assert::assertGreaterThan(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertGreaterThanOrEqual')) {
/**
@no-named-arguments






*/
function assertGreaterThanOrEqual(mixed $minimum, mixed $actual, string $message = ''): void
{
Assert::assertGreaterThanOrEqual(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertLessThan')) {
/**
@no-named-arguments






*/
function assertLessThan(mixed $maximum, mixed $actual, string $message = ''): void
{
Assert::assertLessThan(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertLessThanOrEqual')) {
/**
@no-named-arguments






*/
function assertLessThanOrEqual(mixed $maximum, mixed $actual, string $message = ''): void
{
Assert::assertLessThanOrEqual(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileEquals')) {
/**
@no-named-arguments







*/
function assertFileEquals(string $expected, string $actual, string $message = ''): void
{
Assert::assertFileEquals(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileEqualsCanonicalizing')) {
/**
@no-named-arguments







*/
function assertFileEqualsCanonicalizing(string $expected, string $actual, string $message = ''): void
{
Assert::assertFileEqualsCanonicalizing(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileEqualsIgnoringCase')) {
/**
@no-named-arguments







*/
function assertFileEqualsIgnoringCase(string $expected, string $actual, string $message = ''): void
{
Assert::assertFileEqualsIgnoringCase(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileNotEquals')) {
/**
@no-named-arguments







*/
function assertFileNotEquals(string $expected, string $actual, string $message = ''): void
{
Assert::assertFileNotEquals(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileNotEqualsCanonicalizing')) {
/**
@no-named-arguments







*/
function assertFileNotEqualsCanonicalizing(string $expected, string $actual, string $message = ''): void
{
Assert::assertFileNotEqualsCanonicalizing(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileNotEqualsIgnoringCase')) {
/**
@no-named-arguments







*/
function assertFileNotEqualsIgnoringCase(string $expected, string $actual, string $message = ''): void
{
Assert::assertFileNotEqualsIgnoringCase(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringEqualsFile')) {
/**
@no-named-arguments







*/
function assertStringEqualsFile(string $expectedFile, string $actualString, string $message = ''): void
{
Assert::assertStringEqualsFile(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringEqualsFileCanonicalizing')) {
/**
@no-named-arguments







*/
function assertStringEqualsFileCanonicalizing(string $expectedFile, string $actualString, string $message = ''): void
{
Assert::assertStringEqualsFileCanonicalizing(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringEqualsFileIgnoringCase')) {
/**
@no-named-arguments







*/
function assertStringEqualsFileIgnoringCase(string $expectedFile, string $actualString, string $message = ''): void
{
Assert::assertStringEqualsFileIgnoringCase(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringNotEqualsFile')) {
/**
@no-named-arguments







*/
function assertStringNotEqualsFile(string $expectedFile, string $actualString, string $message = ''): void
{
Assert::assertStringNotEqualsFile(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringNotEqualsFileCanonicalizing')) {
/**
@no-named-arguments







*/
function assertStringNotEqualsFileCanonicalizing(string $expectedFile, string $actualString, string $message = ''): void
{
Assert::assertStringNotEqualsFileCanonicalizing(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringNotEqualsFileIgnoringCase')) {
/**
@no-named-arguments







*/
function assertStringNotEqualsFileIgnoringCase(string $expectedFile, string $actualString, string $message = ''): void
{
Assert::assertStringNotEqualsFileIgnoringCase(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsReadable')) {
/**
@no-named-arguments






*/
function assertIsReadable(string $filename, string $message = ''): void
{
Assert::assertIsReadable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotReadable')) {
/**
@no-named-arguments






*/
function assertIsNotReadable(string $filename, string $message = ''): void
{
Assert::assertIsNotReadable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsWritable')) {
/**
@no-named-arguments






*/
function assertIsWritable(string $filename, string $message = ''): void
{
Assert::assertIsWritable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotWritable')) {
/**
@no-named-arguments






*/
function assertIsNotWritable(string $filename, string $message = ''): void
{
Assert::assertIsNotWritable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertDirectoryExists')) {
/**
@no-named-arguments






*/
function assertDirectoryExists(string $directory, string $message = ''): void
{
Assert::assertDirectoryExists(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertDirectoryDoesNotExist')) {
/**
@no-named-arguments






*/
function assertDirectoryDoesNotExist(string $directory, string $message = ''): void
{
Assert::assertDirectoryDoesNotExist(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertDirectoryIsReadable')) {
/**
@no-named-arguments






*/
function assertDirectoryIsReadable(string $directory, string $message = ''): void
{
Assert::assertDirectoryIsReadable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertDirectoryIsNotReadable')) {
/**
@no-named-arguments






*/
function assertDirectoryIsNotReadable(string $directory, string $message = ''): void
{
Assert::assertDirectoryIsNotReadable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertDirectoryIsWritable')) {
/**
@no-named-arguments






*/
function assertDirectoryIsWritable(string $directory, string $message = ''): void
{
Assert::assertDirectoryIsWritable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertDirectoryIsNotWritable')) {
/**
@no-named-arguments






*/
function assertDirectoryIsNotWritable(string $directory, string $message = ''): void
{
Assert::assertDirectoryIsNotWritable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileExists')) {
/**
@no-named-arguments






*/
function assertFileExists(string $filename, string $message = ''): void
{
Assert::assertFileExists(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileDoesNotExist')) {
/**
@no-named-arguments






*/
function assertFileDoesNotExist(string $filename, string $message = ''): void
{
Assert::assertFileDoesNotExist(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileIsReadable')) {
/**
@no-named-arguments






*/
function assertFileIsReadable(string $file, string $message = ''): void
{
Assert::assertFileIsReadable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileIsNotReadable')) {
/**
@no-named-arguments






*/
function assertFileIsNotReadable(string $file, string $message = ''): void
{
Assert::assertFileIsNotReadable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileIsWritable')) {
/**
@no-named-arguments






*/
function assertFileIsWritable(string $file, string $message = ''): void
{
Assert::assertFileIsWritable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileIsNotWritable')) {
/**
@no-named-arguments






*/
function assertFileIsNotWritable(string $file, string $message = ''): void
{
Assert::assertFileIsNotWritable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertTrue')) {
/**
@phpstan-assert
@no-named-arguments







*/
function assertTrue(mixed $condition, string $message = ''): void
{
Assert::assertTrue(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotTrue')) {
/**
@phpstan-assert
@no-named-arguments







*/
function assertNotTrue(mixed $condition, string $message = ''): void
{
Assert::assertNotTrue(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFalse')) {
/**
@phpstan-assert
@no-named-arguments







*/
function assertFalse(mixed $condition, string $message = ''): void
{
Assert::assertFalse(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotFalse')) {
/**
@phpstan-assert
@no-named-arguments







*/
function assertNotFalse(mixed $condition, string $message = ''): void
{
Assert::assertNotFalse(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNull')) {
/**
@phpstan-assert
@no-named-arguments







*/
function assertNull(mixed $actual, string $message = ''): void
{
Assert::assertNull(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotNull')) {
/**
@phpstan-assert
@no-named-arguments







*/
function assertNotNull(mixed $actual, string $message = ''): void
{
Assert::assertNotNull(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFinite')) {
/**
@no-named-arguments






*/
function assertFinite(mixed $actual, string $message = ''): void
{
Assert::assertFinite(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertInfinite')) {
/**
@no-named-arguments






*/
function assertInfinite(mixed $actual, string $message = ''): void
{
Assert::assertInfinite(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNan')) {
/**
@no-named-arguments






*/
function assertNan(mixed $actual, string $message = ''): void
{
Assert::assertNan(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertObjectHasProperty')) {
/**
@no-named-arguments






*/
function assertObjectHasProperty(string $propertyName, object $object, string $message = ''): void
{
Assert::assertObjectHasProperty(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertObjectNotHasProperty')) {
/**
@no-named-arguments






*/
function assertObjectNotHasProperty(string $propertyName, object $object, string $message = ''): void
{
Assert::assertObjectNotHasProperty(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertSame')) {
/**
@template
@phpstan-assert
@no-named-arguments












*/
function assertSame(mixed $expected, mixed $actual, string $message = ''): void
{
Assert::assertSame(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotSame')) {
/**
@no-named-arguments








*/
function assertNotSame(mixed $expected, mixed $actual, string $message = ''): void
{
Assert::assertNotSame(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertInstanceOf')) {
/**
@template
@phpstan-assert
@no-named-arguments












*/
function assertInstanceOf(string $expected, mixed $actual, string $message = ''): void
{
Assert::assertInstanceOf(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotInstanceOf')) {
/**
@template
@phpstan-assert
@no-named-arguments











*/
function assertNotInstanceOf(string $expected, mixed $actual, string $message = ''): void
{
Assert::assertNotInstanceOf(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsArray')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsArray(mixed $actual, string $message = ''): void
{
Assert::assertIsArray(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsBool')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsBool(mixed $actual, string $message = ''): void
{
Assert::assertIsBool(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsFloat')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsFloat(mixed $actual, string $message = ''): void
{
Assert::assertIsFloat(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsInt')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsInt(mixed $actual, string $message = ''): void
{
Assert::assertIsInt(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNumeric')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsNumeric(mixed $actual, string $message = ''): void
{
Assert::assertIsNumeric(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsObject')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsObject(mixed $actual, string $message = ''): void
{
Assert::assertIsObject(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsResource')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsResource(mixed $actual, string $message = ''): void
{
Assert::assertIsResource(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsClosedResource')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsClosedResource(mixed $actual, string $message = ''): void
{
Assert::assertIsClosedResource(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsString')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsString(mixed $actual, string $message = ''): void
{
Assert::assertIsString(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsScalar')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsScalar(mixed $actual, string $message = ''): void
{
Assert::assertIsScalar(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsCallable')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsCallable(mixed $actual, string $message = ''): void
{
Assert::assertIsCallable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsIterable')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsIterable(mixed $actual, string $message = ''): void
{
Assert::assertIsIterable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotArray')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsNotArray(mixed $actual, string $message = ''): void
{
Assert::assertIsNotArray(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotBool')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsNotBool(mixed $actual, string $message = ''): void
{
Assert::assertIsNotBool(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotFloat')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsNotFloat(mixed $actual, string $message = ''): void
{
Assert::assertIsNotFloat(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotInt')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsNotInt(mixed $actual, string $message = ''): void
{
Assert::assertIsNotInt(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotNumeric')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsNotNumeric(mixed $actual, string $message = ''): void
{
Assert::assertIsNotNumeric(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotObject')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsNotObject(mixed $actual, string $message = ''): void
{
Assert::assertIsNotObject(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotResource')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsNotResource(mixed $actual, string $message = ''): void
{
Assert::assertIsNotResource(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotClosedResource')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsNotClosedResource(mixed $actual, string $message = ''): void
{
Assert::assertIsNotClosedResource(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotString')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsNotString(mixed $actual, string $message = ''): void
{
Assert::assertIsNotString(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotScalar')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsNotScalar(mixed $actual, string $message = ''): void
{
Assert::assertIsNotScalar(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotCallable')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsNotCallable(mixed $actual, string $message = ''): void
{
Assert::assertIsNotCallable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertIsNotIterable')) {
/**
@phpstan-assert
@no-named-arguments








*/
function assertIsNotIterable(mixed $actual, string $message = ''): void
{
Assert::assertIsNotIterable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertMatchesRegularExpression')) {
/**
@no-named-arguments






*/
function assertMatchesRegularExpression(string $pattern, string $string, string $message = ''): void
{
Assert::assertMatchesRegularExpression(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertDoesNotMatchRegularExpression')) {
/**
@no-named-arguments






*/
function assertDoesNotMatchRegularExpression(string $pattern, string $string, string $message = ''): void
{
Assert::assertDoesNotMatchRegularExpression(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertSameSize')) {
/**
@no-named-arguments












*/
function assertSameSize(Countable|iterable $expected, Countable|iterable $actual, string $message = ''): void
{
Assert::assertSameSize(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertNotSameSize')) {
/**
@no-named-arguments












*/
function assertNotSameSize(Countable|iterable $expected, Countable|iterable $actual, string $message = ''): void
{
Assert::assertNotSameSize(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringContainsStringIgnoringLineEndings')) {
/**
@no-named-arguments




*/
function assertStringContainsStringIgnoringLineEndings(string $needle, string $haystack, string $message = ''): void
{
Assert::assertStringContainsStringIgnoringLineEndings(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringEqualsStringIgnoringLineEndings')) {
/**
@no-named-arguments






*/
function assertStringEqualsStringIgnoringLineEndings(string $expected, string $actual, string $message = ''): void
{
Assert::assertStringEqualsStringIgnoringLineEndings(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileMatchesFormat')) {
/**
@no-named-arguments






*/
function assertFileMatchesFormat(string $format, string $actualFile, string $message = ''): void
{
Assert::assertFileMatchesFormat(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertFileMatchesFormatFile')) {
/**
@no-named-arguments






*/
function assertFileMatchesFormatFile(string $formatFile, string $actualFile, string $message = ''): void
{
Assert::assertFileMatchesFormatFile(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringMatchesFormat')) {
/**
@no-named-arguments






*/
function assertStringMatchesFormat(string $format, string $string, string $message = ''): void
{
Assert::assertStringMatchesFormat(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringNotMatchesFormat')) {
/**
@no-named-arguments








*/
function assertStringNotMatchesFormat(string $format, string $string, string $message = ''): void
{
Assert::assertStringNotMatchesFormat(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringMatchesFormatFile')) {
/**
@no-named-arguments






*/
function assertStringMatchesFormatFile(string $formatFile, string $string, string $message = ''): void
{
Assert::assertStringMatchesFormatFile(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringNotMatchesFormatFile')) {
/**
@no-named-arguments








*/
function assertStringNotMatchesFormatFile(string $formatFile, string $string, string $message = ''): void
{
Assert::assertStringNotMatchesFormatFile(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringStartsWith')) {
/**
@no-named-arguments









*/
function assertStringStartsWith(string $prefix, string $string, string $message = ''): void
{
Assert::assertStringStartsWith(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringStartsNotWith')) {
/**
@no-named-arguments









*/
function assertStringStartsNotWith(string $prefix, string $string, string $message = ''): void
{
Assert::assertStringStartsNotWith(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringContainsString')) {
/**
@no-named-arguments




*/
function assertStringContainsString(string $needle, string $haystack, string $message = ''): void
{
Assert::assertStringContainsString(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringContainsStringIgnoringCase')) {
/**
@no-named-arguments




*/
function assertStringContainsStringIgnoringCase(string $needle, string $haystack, string $message = ''): void
{
Assert::assertStringContainsStringIgnoringCase(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringNotContainsString')) {
/**
@no-named-arguments




*/
function assertStringNotContainsString(string $needle, string $haystack, string $message = ''): void
{
Assert::assertStringNotContainsString(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringNotContainsStringIgnoringCase')) {
/**
@no-named-arguments




*/
function assertStringNotContainsStringIgnoringCase(string $needle, string $haystack, string $message = ''): void
{
Assert::assertStringNotContainsStringIgnoringCase(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringEndsWith')) {
/**
@no-named-arguments









*/
function assertStringEndsWith(string $suffix, string $string, string $message = ''): void
{
Assert::assertStringEndsWith(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertStringEndsNotWith')) {
/**
@no-named-arguments









*/
function assertStringEndsNotWith(string $suffix, string $string, string $message = ''): void
{
Assert::assertStringEndsNotWith(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertXmlFileEqualsXmlFile')) {
/**
@no-named-arguments








*/
function assertXmlFileEqualsXmlFile(string $expectedFile, string $actualFile, string $message = ''): void
{
Assert::assertXmlFileEqualsXmlFile(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertXmlFileNotEqualsXmlFile')) {
/**
@no-named-arguments







*/
function assertXmlFileNotEqualsXmlFile(string $expectedFile, string $actualFile, string $message = ''): void
{
Assert::assertXmlFileNotEqualsXmlFile(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertXmlStringEqualsXmlFile')) {
/**
@no-named-arguments







*/
function assertXmlStringEqualsXmlFile(string $expectedFile, string $actualXml, string $message = ''): void
{
Assert::assertXmlStringEqualsXmlFile(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertXmlStringNotEqualsXmlFile')) {
/**
@no-named-arguments







*/
function assertXmlStringNotEqualsXmlFile(string $expectedFile, string $actualXml, string $message = ''): void
{
Assert::assertXmlStringNotEqualsXmlFile(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertXmlStringEqualsXmlString')) {
/**
@no-named-arguments







*/
function assertXmlStringEqualsXmlString(string $expectedXml, string $actualXml, string $message = ''): void
{
Assert::assertXmlStringEqualsXmlString(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertXmlStringNotEqualsXmlString')) {
/**
@no-named-arguments







*/
function assertXmlStringNotEqualsXmlString(string $expectedXml, string $actualXml, string $message = ''): void
{
Assert::assertXmlStringNotEqualsXmlString(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertThat')) {
/**
@no-named-arguments






*/
function assertThat(mixed $value, Constraint $constraint, string $message = ''): void
{
Assert::assertThat(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertJson')) {
/**
@no-named-arguments






*/
function assertJson(string $actual, string $message = ''): void
{
Assert::assertJson(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertJsonStringEqualsJsonString')) {
/**
@no-named-arguments






*/
function assertJsonStringEqualsJsonString(string $expectedJson, string $actualJson, string $message = ''): void
{
Assert::assertJsonStringEqualsJsonString(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertJsonStringNotEqualsJsonString')) {
/**
@no-named-arguments






*/
function assertJsonStringNotEqualsJsonString(string $expectedJson, string $actualJson, string $message = ''): void
{
Assert::assertJsonStringNotEqualsJsonString(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertJsonStringEqualsJsonFile')) {
/**
@no-named-arguments






*/
function assertJsonStringEqualsJsonFile(string $expectedFile, string $actualJson, string $message = ''): void
{
Assert::assertJsonStringEqualsJsonFile(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertJsonStringNotEqualsJsonFile')) {
/**
@no-named-arguments






*/
function assertJsonStringNotEqualsJsonFile(string $expectedFile, string $actualJson, string $message = ''): void
{
Assert::assertJsonStringNotEqualsJsonFile(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertJsonFileEqualsJsonFile')) {
/**
@no-named-arguments






*/
function assertJsonFileEqualsJsonFile(string $expectedFile, string $actualFile, string $message = ''): void
{
Assert::assertJsonFileEqualsJsonFile(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\assertJsonFileNotEqualsJsonFile')) {
/**
@no-named-arguments






*/
function assertJsonFileNotEqualsJsonFile(string $expectedFile, string $actualFile, string $message = ''): void
{
Assert::assertJsonFileNotEqualsJsonFile(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\logicalAnd')) {
function logicalAnd(mixed ...$constraints): LogicalAnd
{
return Assert::logicalAnd(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\logicalOr')) {
function logicalOr(mixed ...$constraints): LogicalOr
{
return Assert::logicalOr(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\logicalNot')) {
function logicalNot(Constraint $constraint): LogicalNot
{
return Assert::logicalNot(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\logicalXor')) {
function logicalXor(mixed ...$constraints): LogicalXor
{
return Assert::logicalXor(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\anything')) {
function anything(): IsAnything
{
return Assert::anything(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isTrue')) {
function isTrue(): IsTrue
{
return Assert::isTrue(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isFalse')) {
function isFalse(): IsFalse
{
return Assert::isFalse(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isJson')) {
function isJson(): IsJson
{
return Assert::isJson(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isNull')) {
function isNull(): IsNull
{
return Assert::isNull(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isFinite')) {
function isFinite(): IsFinite
{
return Assert::isFinite(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isInfinite')) {
function isInfinite(): IsInfinite
{
return Assert::isInfinite(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isNan')) {
function isNan(): IsNan
{
return Assert::isNan(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsEqual')) {
function containsEqual(mixed $value): TraversableContainsEqual
{
return Assert::containsEqual(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsIdentical')) {
function containsIdentical(mixed $value): TraversableContainsIdentical
{
return Assert::containsIdentical(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnly')) {
function containsOnly(string $type): TraversableContainsOnly
{
return Assert::containsOnly(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyArray')) {
function containsOnlyArray(): TraversableContainsOnly
{
return Assert::containsOnlyArray(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyBool')) {
function containsOnlyBool(): TraversableContainsOnly
{
return Assert::containsOnlyBool(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyCallable')) {
function containsOnlyCallable(): TraversableContainsOnly
{
return Assert::containsOnlyCallable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyFloat')) {
function containsOnlyFloat(): TraversableContainsOnly
{
return Assert::containsOnlyFloat(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyInt')) {
function containsOnlyInt(): TraversableContainsOnly
{
return Assert::containsOnlyInt(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyIterable')) {
function containsOnlyIterable(): TraversableContainsOnly
{
return Assert::containsOnlyIterable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyNull')) {
function containsOnlyNull(): TraversableContainsOnly
{
return Assert::containsOnlyNull(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyNumeric')) {
function containsOnlyNumeric(): TraversableContainsOnly
{
return Assert::containsOnlyNumeric(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyObject')) {
function containsOnlyObject(): TraversableContainsOnly
{
return Assert::containsOnlyObject(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyResource')) {
function containsOnlyResource(): TraversableContainsOnly
{
return Assert::containsOnlyResource(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyClosedResource')) {
function containsOnlyClosedResource(): TraversableContainsOnly
{
return Assert::containsOnlyClosedResource(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyScalar')) {
function containsOnlyScalar(): TraversableContainsOnly
{
return Assert::containsOnlyScalar(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyString')) {
function containsOnlyString(): TraversableContainsOnly
{
return Assert::containsOnlyString(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\containsOnlyInstancesOf')) {
function containsOnlyInstancesOf(string $className): TraversableContainsOnly
{
return Assert::containsOnlyInstancesOf(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\arrayHasKey')) {
function arrayHasKey(mixed $key): ArrayHasKey
{
return Assert::arrayHasKey(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isList')) {
function isList(): IsList
{
return Assert::isList(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\equalTo')) {
function equalTo(mixed $value): IsEqual
{
return Assert::equalTo(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\equalToCanonicalizing')) {
function equalToCanonicalizing(mixed $value): IsEqualCanonicalizing
{
return Assert::equalToCanonicalizing(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\equalToIgnoringCase')) {
function equalToIgnoringCase(mixed $value): IsEqualIgnoringCase
{
return Assert::equalToIgnoringCase(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\equalToWithDelta')) {
function equalToWithDelta(mixed $value, float $delta): IsEqualWithDelta
{
return Assert::equalToWithDelta(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isEmpty')) {
function isEmpty(): IsEmpty
{
return Assert::isEmpty(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isWritable')) {
function isWritable(): IsWritable
{
return Assert::isWritable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isReadable')) {
function isReadable(): IsReadable
{
return Assert::isReadable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\directoryExists')) {
function directoryExists(): DirectoryExists
{
return Assert::directoryExists(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\fileExists')) {
function fileExists(): FileExists
{
return Assert::fileExists(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\greaterThan')) {
function greaterThan(mixed $value): GreaterThan
{
return Assert::greaterThan(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\greaterThanOrEqual')) {
function greaterThanOrEqual(mixed $value): LogicalOr
{
return Assert::greaterThanOrEqual(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\identicalTo')) {
function identicalTo(mixed $value): IsIdentical
{
return Assert::identicalTo(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isInstanceOf')) {
function isInstanceOf(string $className): IsInstanceOf
{
return Assert::isInstanceOf(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isArray')) {
function isArray(): IsType
{
return Assert::isArray(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isBool')) {
function isBool(): IsType
{
return Assert::isBool(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isCallable')) {
function isCallable(): IsType
{
return Assert::isCallable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isFloat')) {
function isFloat(): IsType
{
return Assert::isFloat(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isInt')) {
function isInt(): IsType
{
return Assert::isInt(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isIterable')) {
function isIterable(): IsType
{
return Assert::isIterable(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isNumeric')) {
function isNumeric(): IsType
{
return Assert::isNumeric(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isObject')) {
function isObject(): IsType
{
return Assert::isObject(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isResource')) {
function isResource(): IsType
{
return Assert::isResource(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isClosedResource')) {
function isClosedResource(): IsType
{
return Assert::isClosedResource(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isScalar')) {
function isScalar(): IsType
{
return Assert::isScalar(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isString')) {
function isString(): IsType
{
return Assert::isString(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\isType')) {
function isType(string $type): IsType
{
return Assert::isType(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\lessThan')) {
function lessThan(mixed $value): LessThan
{
return Assert::lessThan(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\lessThanOrEqual')) {
function lessThanOrEqual(mixed $value): LogicalOr
{
return Assert::lessThanOrEqual(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\matchesRegularExpression')) {
function matchesRegularExpression(string $pattern): RegularExpression
{
return Assert::matchesRegularExpression(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\matches')) {
function matches(string $string): StringMatchesFormatDescription
{
return Assert::matches(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\stringStartsWith')) {
function stringStartsWith(string $prefix): StringStartsWith
{
return Assert::stringStartsWith(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\stringContains')) {
function stringContains(string $string, bool $case = true): StringContains
{
return Assert::stringContains(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\stringEndsWith')) {
function stringEndsWith(string $suffix): StringEndsWith
{
return Assert::stringEndsWith(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\stringEqualsStringIgnoringLineEndings')) {
function stringEqualsStringIgnoringLineEndings(string $string): StringEqualsStringIgnoringLineEndings
{
return Assert::stringEqualsStringIgnoringLineEndings(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\countOf')) {
function countOf(int $count): Count
{
return Assert::countOf(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\objectEquals')) {
function objectEquals(object $object, string $method = 'equals'): ObjectEquals
{
return Assert::objectEquals(...func_get_args());
}
}

if (!function_exists('PHPUnit\Framework\callback')) {
/**
@template




*/
function callback(callable $callback): Callback
{
return Assert::callback($callback);
}
}

if (!function_exists('PHPUnit\Framework\any')) {




function any(): AnyInvokedCountMatcher
{
return new AnyInvokedCountMatcher;
}
}

if (!function_exists('PHPUnit\Framework\never')) {



function never(): InvokedCountMatcher
{
return new InvokedCountMatcher(0);
}
}

if (!function_exists('PHPUnit\Framework\atLeast')) {




function atLeast(int $requiredInvocations): InvokedAtLeastCountMatcher
{
return new InvokedAtLeastCountMatcher(
$requiredInvocations,
);
}
}

if (!function_exists('PHPUnit\Framework\atLeastOnce')) {



function atLeastOnce(): InvokedAtLeastOnceMatcher
{
return new InvokedAtLeastOnceMatcher;
}
}

if (!function_exists('PHPUnit\Framework\once')) {



function once(): InvokedCountMatcher
{
return new InvokedCountMatcher(1);
}
}

if (!function_exists('PHPUnit\Framework\exactly')) {




function exactly(int $count): InvokedCountMatcher
{
return new InvokedCountMatcher($count);
}
}

if (!function_exists('PHPUnit\Framework\atMost')) {




function atMost(int $allowedInvocations): InvokedAtMostCountMatcher
{
return new InvokedAtMostCountMatcher($allowedInvocations);
}
}

if (!function_exists('PHPUnit\Framework\returnValue')) {
function returnValue(mixed $value): ReturnStub
{
return new ReturnStub($value);
}
}

if (!function_exists('PHPUnit\Framework\returnValueMap')) {



function returnValueMap(array $valueMap): ReturnValueMapStub
{
return new ReturnValueMapStub($valueMap);
}
}

if (!function_exists('PHPUnit\Framework\returnArgument')) {
function returnArgument(int $argumentIndex): ReturnArgumentStub
{
return new ReturnArgumentStub($argumentIndex);
}
}

if (!function_exists('PHPUnit\Framework\returnCallback')) {
function returnCallback(callable $callback): ReturnCallbackStub
{
return new ReturnCallbackStub($callback);
}
}

if (!function_exists('PHPUnit\Framework\returnSelf')) {





function returnSelf(): ReturnSelfStub
{
return new ReturnSelfStub;
}
}

if (!function_exists('PHPUnit\Framework\throwException')) {
function throwException(Throwable $exception): ExceptionStub
{
return new ExceptionStub($exception);
}
}

if (!function_exists('PHPUnit\Framework\onConsecutiveCalls')) {
function onConsecutiveCalls(): ConsecutiveCallsStub
{
$arguments = func_get_args();

return new ConsecutiveCallsStub($arguments);
}
}
