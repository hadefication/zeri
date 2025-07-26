<?php
namespace Hamcrest\Text;




use Hamcrest\BaseMatcher;
use Hamcrest\Core\AnyOf;
use Hamcrest\Core\IsNull;
use Hamcrest\Description;




class IsEmptyString extends BaseMatcher
{

private static $_INSTANCE;
private static $_NULL_OR_EMPTY_INSTANCE;
private static $_NOT_INSTANCE;

private $_empty;

public function __construct($empty = true)
{
$this->_empty = $empty;
}

public function matches($item)
{
return $this->_empty
? ($item === '')
: is_string($item) && $item !== '';
}

public function describeTo(Description $description)
{
$description->appendText($this->_empty ? 'an empty string' : 'a non-empty string');
}

/**
@factory


*/
public static function isEmptyString()
{
if (!self::$_INSTANCE) {
self::$_INSTANCE = new self(true);
}

return self::$_INSTANCE;
}

/**
@factory


*/
public static function isEmptyOrNullString()
{
if (!self::$_NULL_OR_EMPTY_INSTANCE) {
self::$_NULL_OR_EMPTY_INSTANCE = AnyOf::anyOf(
IsNull::nullvalue(),
self::isEmptyString()
);
}

return self::$_NULL_OR_EMPTY_INSTANCE;
}

/**
@factory


*/
public static function isNonEmptyString()
{
if (!self::$_NOT_INSTANCE) {
self::$_NOT_INSTANCE = new self(false);
}

return self::$_NOT_INSTANCE;
}
}
