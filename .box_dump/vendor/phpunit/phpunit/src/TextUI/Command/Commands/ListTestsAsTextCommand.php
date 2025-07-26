<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Command;

use const PHP_EOL;
use function count;
use function sprintf;
use function str_replace;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\PhptTestCase;

/**
@no-named-arguments


*/
final readonly class ListTestsAsTextCommand implements Command
{



private array $tests;




public function __construct(array $tests)
{
$this->tests = $tests;
}

public function execute(): Result
{
$buffer = sprintf(
'Available test%s:' . PHP_EOL,
count($this->tests) > 1 ? 's' : '',
);

foreach ($this->tests as $test) {
if ($test instanceof TestCase) {
$name = sprintf(
'%s::%s',
$test::class,
str_replace(' with data set ', '', $test->nameWithDataSet()),
);
} else {
$name = $test->getName();
}

$buffer .= sprintf(
' - %s' . PHP_EOL,
$name,
);
}

return Result::from($buffer);
}
}
