<?php









namespace Mockery\Matcher;

class Not extends MatcherAbstract
{





public function __toString()
{
return '<Not>';
}

/**
@template







*/
public function match(&$actual)
{
return $actual !== $this->_expected;
}
}
