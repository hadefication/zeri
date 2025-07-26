<?php









namespace Mockery\Matcher;

use function class_exists;
use function function_exists;
use function interface_exists;
use function is_string;
use function strtolower;
use function ucfirst;

class Type extends MatcherAbstract
{





public function __toString()
{
return '<' . ucfirst($this->_expected) . '>';
}

/**
@template






*/
public function match(&$actual)
{
$function = $this->_expected === 'real' ? 'is_float' : 'is_' . strtolower($this->_expected);

if (function_exists($function)) {
return $function($actual);
}

if (! is_string($this->_expected)) {
return false;
}

if (class_exists($this->_expected) || interface_exists($this->_expected)) {
return $actual instanceof $this->_expected;
}

return false;
}
}
