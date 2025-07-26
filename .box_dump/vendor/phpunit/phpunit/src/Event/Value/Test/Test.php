<?php declare(strict_types=1);








namespace PHPUnit\Event\Code;

/**
@immutable
@no-named-arguments

*/
abstract readonly class Test
{



private string $file;




public function __construct(string $file)
{
$this->file = $file;
}




public function file(): string
{
return $this->file;
}

/**
@phpstan-assert-if-true
*/
public function isTestMethod(): bool
{
return false;
}

/**
@phpstan-assert-if-true
*/
public function isPhpt(): bool
{
return false;
}




abstract public function id(): string;




abstract public function name(): string;
}
