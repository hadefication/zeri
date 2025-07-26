<?php

declare(strict_types=1);










namespace GrahamCampbell\ResultType;

use PhpOption\None;
use PhpOption\Some;

/**
@template
@template
@extends

*/
final class Error extends Result
{



private $value;








private function __construct($value)
{
$this->value = $value;
}

/**
@template






*/
public static function create($value)
{
return new self($value);
}






public function success()
{
return None::create();
}

/**
@template






*/
public function map(callable $f)
{
return self::create($this->value);
}

/**
@template
@template






*/
public function flatMap(callable $f)
{

return self::create($this->value);
}






public function error()
{
return Some::create($this->value);
}

/**
@template






*/
public function mapError(callable $f)
{
return self::create($f($this->value));
}
}
