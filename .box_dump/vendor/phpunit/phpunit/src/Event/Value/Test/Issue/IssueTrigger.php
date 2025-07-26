<?php declare(strict_types=1);








namespace PHPUnit\Event\Code\IssueTrigger;

/**
@immutable
@no-named-arguments

*/
abstract class IssueTrigger
{
public static function test(): TestTrigger
{
return new TestTrigger;
}

public static function self(): SelfTrigger
{
return new SelfTrigger;
}

public static function direct(): DirectTrigger
{
return new DirectTrigger;
}

public static function indirect(): IndirectTrigger
{
return new IndirectTrigger;
}

public static function unknown(): UnknownTrigger
{
return new UnknownTrigger;
}

final private function __construct()
{
}

/**
@phpstan-assert-if-true


*/
public function isTest(): bool
{
return false;
}

/**
@phpstan-assert-if-true


*/
public function isSelf(): bool
{
return false;
}

/**
@phpstan-assert-if-true


*/
public function isDirect(): bool
{
return false;
}

/**
@phpstan-assert-if-true


*/
public function isIndirect(): bool
{
return false;
}

/**
@phpstan-assert-if-true
*/
public function isUnknown(): bool
{
return false;
}

abstract public function asString(): string;
}
