<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class TestDox extends Metadata
{



private string $text;





protected function __construct(int $level, string $text)
{
parent::__construct($level);

$this->text = $text;
}

public function isTestDox(): true
{
return true;
}




public function text(): string
{
return $this->text;
}
}
