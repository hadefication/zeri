<?php declare(strict_types=1);








namespace PHPUnit\Logging\TestDox;

use function sprintf;

/**
@no-named-arguments


*/
final readonly class PlainTextRenderer
{



public function render(array $tests): string
{
$buffer = '';

foreach ($tests as $prettifiedClassName => $_tests) {
$buffer .= $prettifiedClassName . "\n";

foreach ($this->reduce($_tests) as $prettifiedMethodName => $outcome) {
$buffer .= sprintf(
' [%s] %s' . "\n",
$outcome,
$prettifiedMethodName,
);
}

$buffer .= "\n";
}

return $buffer;
}




private function reduce(TestResultCollection $tests): array
{
$result = [];

foreach ($tests as $test) {
$prettifiedMethodName = $test->test()->testDox()->prettifiedMethodName();

$success = true;

if ($test->status()->isError() ||
$test->status()->isFailure() ||
$test->status()->isIncomplete() ||
$test->status()->isSkipped()) {
$success = false;
}

if (!isset($result[$prettifiedMethodName])) {
$result[$prettifiedMethodName] = $success ? 'x' : ' ';

continue;
}

if ($success) {
continue;
}

$result[$prettifiedMethodName] = ' ';
}

return $result;
}
}
