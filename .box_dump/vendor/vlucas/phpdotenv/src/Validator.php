<?php

declare(strict_types=1);

namespace Dotenv;

use Dotenv\Exception\ValidationException;
use Dotenv\Repository\RepositoryInterface;
use Dotenv\Util\Regex;
use Dotenv\Util\Str;

class Validator
{





private $repository;






private $variables;









public function __construct(RepositoryInterface $repository, array $variables)
{
$this->repository = $repository;
$this->variables = $variables;
}








public function required()
{
return $this->assert(
static function (?string $value) {
return $value !== null;
},
'is missing'
);
}








public function notEmpty()
{
return $this->assertNullable(
static function (string $value) {
return Str::len(\trim($value)) > 0;
},
'is empty'
);
}








public function isInteger()
{
return $this->assertNullable(
static function (string $value) {
return \ctype_digit($value);
},
'is not an integer'
);
}








public function isBoolean()
{
return $this->assertNullable(
static function (string $value) {
if ($value === '') {
return false;
}

return \filter_var($value, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE) !== null;
},
'is not a boolean'
);
}










public function allowedValues(array $choices)
{
return $this->assertNullable(
static function (string $value) use ($choices) {
return \in_array($value, $choices, true);
},
\sprintf('is not one of [%s]', \implode(', ', $choices))
);
}










public function allowedRegexValues(string $regex)
{
return $this->assertNullable(
static function (string $value) use ($regex) {
return Regex::matches($regex, $value)->success()->getOrElse(false);
},
\sprintf('does not match "%s"', $regex)
);
}











public function assert(callable $callback, string $message)
{
$failing = [];

foreach ($this->variables as $variable) {
if ($callback($this->repository->get($variable)) === false) {
$failing[] = \sprintf('%s %s', $variable, $message);
}
}

if (\count($failing) > 0) {
throw new ValidationException(\sprintf(
'One or more environment variables failed assertions: %s.',
\implode(', ', $failing)
));
}

return $this;
}













public function assertNullable(callable $callback, string $message)
{
return $this->assert(
static function (?string $value) use ($callback) {
if ($value === null) {
return true;
}

return $callback($value);
},
$message
);
}
}
