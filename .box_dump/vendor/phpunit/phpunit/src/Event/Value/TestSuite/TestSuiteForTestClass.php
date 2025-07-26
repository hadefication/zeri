<?php declare(strict_types=1);








namespace PHPUnit\Event\TestSuite;

use PHPUnit\Event\Code\TestCollection;

/**
@immutable
@no-named-arguments

*/
final readonly class TestSuiteForTestClass extends TestSuite
{



private string $className;
private string $file;
private int $line;




public function __construct(string $name, int $size, TestCollection $tests, string $file, int $line)
{
parent::__construct($name, $size, $tests);

$this->className = $name;
$this->file = $file;
$this->line = $line;
}




public function className(): string
{
return $this->className;
}

public function file(): string
{
return $this->file;
}

public function line(): int
{
return $this->line;
}

public function isForTestClass(): true
{
return true;
}
}
