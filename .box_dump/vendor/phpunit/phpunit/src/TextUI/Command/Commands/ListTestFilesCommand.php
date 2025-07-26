<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Command;

use const PHP_EOL;
use function array_unique;
use function assert;
use function sprintf;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\PhptTestCase;
use ReflectionClass;
use ReflectionException;

/**
@no-named-arguments


*/
final readonly class ListTestFilesCommand implements Command
{



private array $tests;




public function __construct(array $tests)
{
$this->tests = $tests;
}




public function execute(): Result
{
$buffer = 'Available test files:' . PHP_EOL;

$results = [];

foreach ($this->tests as $test) {
if ($test instanceof TestCase) {
$name = (new ReflectionClass($test))->getFileName();

assert($name !== false);

$results[] = $name;

continue;
}

$results[] = $test->getName();
}

foreach (array_unique($results) as $result) {
$buffer .= sprintf(
' - %s' . PHP_EOL,
$result,
);
}

return Result::from($buffer);
}
}
