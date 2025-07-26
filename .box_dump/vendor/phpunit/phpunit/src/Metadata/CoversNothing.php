<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class CoversNothing extends Metadata
{
public function isCoversNothing(): true
{
return true;
}
}
