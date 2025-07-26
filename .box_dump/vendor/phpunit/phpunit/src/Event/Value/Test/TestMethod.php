<?php declare(strict_types=1);








namespace PHPUnit\Event\Code;

use function is_int;
use function sprintf;
use PHPUnit\Event\TestData\TestDataCollection;
use PHPUnit\Metadata\MetadataCollection;

/**
@immutable
@no-named-arguments

*/
final readonly class TestMethod extends Test
{



private string $className;




private string $methodName;




private int $line;
private TestDox $testDox;
private MetadataCollection $metadata;
private TestDataCollection $testData;







public function __construct(string $className, string $methodName, string $file, int $line, TestDox $testDox, MetadataCollection $metadata, TestDataCollection $testData)
{
parent::__construct($file);

$this->className = $className;
$this->methodName = $methodName;
$this->line = $line;
$this->testDox = $testDox;
$this->metadata = $metadata;
$this->testData = $testData;
}




public function className(): string
{
return $this->className;
}




public function methodName(): string
{
return $this->methodName;
}




public function line(): int
{
return $this->line;
}

public function testDox(): TestDox
{
return $this->testDox;
}

public function metadata(): MetadataCollection
{
return $this->metadata;
}

public function testData(): TestDataCollection
{
return $this->testData;
}

public function isTestMethod(): true
{
return true;
}




public function id(): string
{
$buffer = $this->className . '::' . $this->methodName;

if ($this->testData()->hasDataFromDataProvider()) {
$buffer .= '#' . $this->testData->dataFromDataProvider()->dataSetName();
}

return $buffer;
}




public function nameWithClass(): string
{
return $this->className . '::' . $this->name();
}




public function name(): string
{
if (!$this->testData->hasDataFromDataProvider()) {
return $this->methodName;
}

$dataSetName = $this->testData->dataFromDataProvider()->dataSetName();

if (is_int($dataSetName)) {
$dataSetName = sprintf(
' with data set #%d',
$dataSetName,
);
} else {
$dataSetName = sprintf(
' with data set "%s"',
$dataSetName,
);
}

return $this->methodName . $dataSetName;
}
}
