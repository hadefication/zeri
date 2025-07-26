<?php









namespace Mockery\Matcher;

class AndAnyOtherArgs extends MatcherAbstract
{





public function __toString()
{
return '<AndAnyOthers>';
}

/**
@template






*/
public function match(&$actual)
{
return true;
}
}
