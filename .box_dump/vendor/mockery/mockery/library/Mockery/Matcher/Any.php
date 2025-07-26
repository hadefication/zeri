<?php









namespace Mockery\Matcher;

class Any extends MatcherAbstract
{





public function __toString()
{
return '<Any>';
}

/**
@template






*/
public function match(&$actual)
{
return true;
}
}
