<?php declare(strict_types=1);








namespace PHPUnit\Framework\TestStatus;

/**
@immutable
@no-named-arguments



*/
abstract readonly class Known extends TestStatus
{
public function isKnown(): true
{
return true;
}
}
