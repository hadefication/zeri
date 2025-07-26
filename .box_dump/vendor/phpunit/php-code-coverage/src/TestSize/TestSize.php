<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Test\TestSize;

/**
@immutable
*/
abstract class TestSize
{
public static function unknown(): Unknown
{
return new Unknown;
}

public static function small(): Small
{
return new Small;
}

public static function medium(): Medium
{
return new Medium;
}

public static function large(): Large
{
return new Large;
}

/**
@phpstan-assert-if-true
*/
public function isKnown(): bool
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

/**
@phpstan-assert-if-true
*/
public function isSmall(): bool
{
return false;
}

/**
@phpstan-assert-if-true
*/
public function isMedium(): bool
{
return false;
}

/**
@phpstan-assert-if-true
*/
public function isLarge(): bool
{
return false;
}

abstract public function asString(): string;
}
