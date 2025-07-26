<?php

declare(strict_types=1);










namespace GrahamCampbell\ResultType;

/**
@template
@template
*/
abstract class Result
{





abstract public function success();

/**
@template






*/
abstract public function map(callable $f);

/**
@template
@template






*/
abstract public function flatMap(callable $f);






abstract public function error();

/**
@template






*/
abstract public function mapError(callable $f);
}
