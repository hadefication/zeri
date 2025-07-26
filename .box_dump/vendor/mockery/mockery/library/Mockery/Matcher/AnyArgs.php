<?php









namespace Mockery\Matcher;

class AnyArgs extends MatcherAbstract implements ArgumentListMatcher
{
public function __toString()
{
return '<Any Arguments>';
}

/**
@template




*/
public function match(&$actual)
{
return true;
}
}
