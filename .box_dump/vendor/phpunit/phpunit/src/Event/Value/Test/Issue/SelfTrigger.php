<?php declare(strict_types=1);








namespace PHPUnit\Event\Code\IssueTrigger;

/**
@immutable
@no-named-arguments

*/
final class SelfTrigger extends IssueTrigger
{



public function isSelf(): true
{
return true;
}

public function asString(): string
{
return 'issue triggered by first-party code calling into first-party code';
}
}
