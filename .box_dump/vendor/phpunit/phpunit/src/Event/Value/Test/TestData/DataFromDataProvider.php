<?php declare(strict_types=1);








namespace PHPUnit\Event\TestData;

/**
@immutable
@no-named-arguments

*/
final readonly class DataFromDataProvider extends TestData
{
private int|string $dataSetName;
private string $dataAsStringForResultOutput;

public static function from(int|string $dataSetName, string $data, string $dataAsStringForResultOutput): self
{
return new self($dataSetName, $data, $dataAsStringForResultOutput);
}

protected function __construct(int|string $dataSetName, string $data, string $dataAsStringForResultOutput)
{
$this->dataSetName = $dataSetName;
$this->dataAsStringForResultOutput = $dataAsStringForResultOutput;

parent::__construct($data);
}

public function dataSetName(): int|string
{
return $this->dataSetName;
}




public function dataAsStringForResultOutput(): string
{
return $this->dataAsStringForResultOutput;
}

public function isFromDataProvider(): true
{
return true;
}
}
