<?php declare(strict_types=1);








namespace PHPUnit\Event\Code\IssueTrigger;

/**
@immutable
@no-named-arguments

*/
final class IndirectTrigger extends IssueTrigger
{



public function isIndirect(): true
{
return true;
}

public function asString(): string
{
return 'issue triggered by third-party code';
}
}
