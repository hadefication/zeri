<?php declare(strict_types=1);








namespace PHPUnit\Event\Code\IssueTrigger;

/**
@immutable
@no-named-arguments

*/
final class TestTrigger extends IssueTrigger
{



public function isTest(): true
{
return true;
}

public function asString(): string
{
return 'issue triggered by test code';
}
}
