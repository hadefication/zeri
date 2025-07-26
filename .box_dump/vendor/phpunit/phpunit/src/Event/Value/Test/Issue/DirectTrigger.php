<?php declare(strict_types=1);








namespace PHPUnit\Event\Code\IssueTrigger;

/**
@immutable
@no-named-arguments

*/
final class DirectTrigger extends IssueTrigger
{



public function isDirect(): true
{
return true;
}

public function asString(): string
{
return 'issue triggered by first-party code calling into third-party code';
}
}
