<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class DoesNotPerformAssertions extends Metadata
{
public function isDoesNotPerformAssertions(): true
{
return true;
}
}
