<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Command;

use const PHP_EOL;
use function file_put_contents;
use function ksort;
use function sprintf;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\PhptTestCase;
use XMLWriter;

/**
@no-named-arguments


*/
final readonly class ListTestsAsXmlCommand implements Command
{



private array $tests;
private string $filename;




public function __construct(array $tests, string $filename)
{
$this->tests = $tests;
$this->filename = $filename;
}

public function execute(): Result
{
$writer = new XMLWriter;

$writer->openMemory();
$writer->setIndent(true);
$writer->startDocument();

$writer->startElement('testSuite');
$writer->writeAttribute('xmlns', 'https://xml.phpunit.de/testSuite');

$writer->startElement('tests');

$currentTestClass = null;
$groups = [];

foreach ($this->tests as $test) {
if ($test instanceof TestCase) {
foreach ($test->groups() as $group) {
if (!isset($groups[$group])) {
$groups[$group] = [];
}

$groups[$group][] = $test->valueObjectForEvents()->id();
}

if ($test::class !== $currentTestClass) {
if ($currentTestClass !== null) {
$writer->endElement();
}

$writer->startElement('testClass');
$writer->writeAttribute('name', $test::class);
$writer->writeAttribute('file', $test->valueObjectForEvents()->file());

$currentTestClass = $test::class;
}

$writer->startElement('testMethod');
$writer->writeAttribute('id', $test->valueObjectForEvents()->id());
$writer->writeAttribute('name', $test->valueObjectForEvents()->methodName());
$writer->endElement();

continue;
}

if ($currentTestClass !== null) {
$writer->endElement();

$currentTestClass = null;
}

$writer->startElement('phpt');
$writer->writeAttribute('file', $test->getName());
$writer->endElement();
}

if ($currentTestClass !== null) {
$writer->endElement();
}

$writer->endElement();

ksort($groups);

$writer->startElement('groups');

foreach ($groups as $groupName => $testIds) {
$writer->startElement('group');
$writer->writeAttribute('name', (string) $groupName);

foreach ($testIds as $testId) {
$writer->startElement('test');
$writer->writeAttribute('id', $testId);
$writer->endElement();
}

$writer->endElement();
}

$writer->endElement();
$writer->endElement();

file_put_contents($this->filename, $writer->outputMemory());

return Result::from(
sprintf(
'Wrote list of tests that would have been run to %s' . PHP_EOL,
$this->filename,
),
);
}
}
