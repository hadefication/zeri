<?php declare(strict_types=1);








namespace PHPUnit\Event\TestSuite;

use PHPUnit\Event\Code\TestCollection;

/**
@immutable
@no-named-arguments

*/
final readonly class TestSuiteForTestMethodWithDataProvider extends TestSuite
{



private string $className;




private string $methodName;
private string $file;
private int $line;






public function __construct(string $name, int $size, TestCollection $tests, string $className, string $methodName, string $file, int $line)
{
parent::__construct($name, $size, $tests);

$this->className = $className;
$this->methodName = $methodName;
$this->file = $file;
$this->line = $line;
}




public function className(): string
{
return $this->className;
}




public function methodName(): string
{
return $this->methodName;
}

public function file(): string
{
return $this->file;
}

public function line(): int
{
return $this->line;
}

public function isForTestMethodWithDataProvider(): true
{
return true;
}
}
