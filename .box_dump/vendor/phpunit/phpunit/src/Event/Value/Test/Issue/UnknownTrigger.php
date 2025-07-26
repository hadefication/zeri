<?php declare(strict_types=1);








namespace PHPUnit\Event\Code\IssueTrigger;

/**
@immutable
@no-named-arguments

*/
final class UnknownTrigger extends IssueTrigger
{
public function isUnknown(): true
{
return true;
}

public function asString(): string
{
return 'unknown if issue was triggered in first-party code or third-party code';
}
}
