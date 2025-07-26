<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class Test extends Metadata
{
public function isTest(): true
{
return true;
}
}
