<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class RunInSeparateProcess extends Metadata
{
public function isRunInSeparateProcess(): true
{
return true;
}
}
