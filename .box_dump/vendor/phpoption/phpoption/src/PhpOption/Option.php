<?php

















namespace PhpOption;

use ArrayAccess;
use IteratorAggregate;

/**
@template
@implements

*/
abstract class Option implements IteratorAggregate
{
/**
@template












*/
public static function fromValue($value, $noneValue = null)
{
if ($value === $noneValue) {
return None::create();
}

return new Some($value);
}

/**
@template











*/
public static function fromArraysValue($array, $key)
{
if ($key === null || !(is_array($array) || $array instanceof ArrayAccess) || !isset($array[$key])) {
return None::create();
}

return new Some($array[$key]);
}

/**
@template













*/
public static function fromReturn($callback, array $arguments = [], $noneValue = null)
{
return new LazyOption(static function () use ($callback, $arguments, $noneValue) {

$return = call_user_func_array($callback, $arguments);

if ($return === $noneValue) {
return None::create();
}

return new Some($return);
});
}

/**
@template













*/
public static function ensure($value, $noneValue = null)
{
if ($value instanceof self) {
return $value;
} elseif (is_callable($value)) {
return new LazyOption(static function () use ($value, $noneValue) {

$return = $value();

if ($return instanceof self) {
return $return;
} else {
return self::fromValue($return, $noneValue);
}
});
} else {
return self::fromValue($value, $noneValue);
}
}

/**
@template













*/
public static function lift($callback, $noneValue = null)
{
return static function () use ($callback, $noneValue) {

$args = func_get_args();

$reduced_args = array_reduce(
$args,

static function ($status, self $o) {
return $o->isEmpty() ? true : $status;
},
false
);

if ($reduced_args) {
return None::create();
}

$args = array_map(

static function (self $o) {



return $o->get();
},
$args
);

return self::ensure(call_user_func_array($callback, $args), $noneValue);
};
}








abstract public function get();

/**
@template






*/
abstract public function getOrElse($default);

/**
@template









*/
abstract public function getOrCall($callable);








abstract public function getOrThrow(\Exception $ex);






abstract public function isEmpty();






abstract public function isDefined();

















abstract public function orElse(self $else);























abstract public function ifDefined($callable);












abstract public function forAll($callable);

/**
@template













*/
abstract public function map($callable);

/**
@template










*/
abstract public function flatMap($callable);











abstract public function filter($callable);











abstract public function filterNot($callable);














abstract public function select($value);














abstract public function reject($value);

/**
@template

























*/
abstract public function foldLeft($initialValue, $callable);

/**
@template







*/
abstract public function foldRight($initialValue, $callable);
}
