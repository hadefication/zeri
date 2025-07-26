<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Command;

use const PHP_EOL;
use function count;
use function ksort;
use function sprintf;
use function str_starts_with;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\PhptTestCase;

/**
@no-named-arguments


*/
final readonly class ListGroupsCommand implements Command
{



private array $tests;




public function __construct(array $tests)
{
$this->tests = $tests;
}

public function execute(): Result
{

$groups = [];

foreach ($this->tests as $test) {
if ($test instanceof PhptTestCase) {
if (!isset($groups['default'])) {
$groups['default'] = 1;
} else {
$groups['default']++;
}

continue;
}

foreach ($test->groups() as $group) {
if (!isset($groups[$group])) {
$groups[$group] = 1;
} else {
$groups[$group]++;
}
}
}

ksort($groups);

$buffer = sprintf(
'Available test group%s:' . PHP_EOL,
count($groups) > 1 ? 's' : '',
);

foreach ($groups as $group => $numberOfTests) {
if (str_starts_with((string) $group, '__phpunit_')) {
continue;
}

$buffer .= sprintf(
' - %s (%d test%s)' . PHP_EOL,
(string) $group,
$numberOfTests,
$numberOfTests > 1 ? 's' : '',
);
}

return Result::from($buffer);
}
}
