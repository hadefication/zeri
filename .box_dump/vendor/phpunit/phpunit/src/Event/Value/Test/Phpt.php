<?php declare(strict_types=1);








namespace PHPUnit\Event\Code;

/**
@immutable
@no-named-arguments

*/
final readonly class Phpt extends Test
{
public function isPhpt(): true
{
return true;
}




public function id(): string
{
return $this->file();
}




public function name(): string
{
return $this->file();
}
}
