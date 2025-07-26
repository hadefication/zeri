<?php









namespace Mockery\Matcher;

class NotAnyOf extends MatcherAbstract
{





public function __toString()
{
return '<AnyOf>';
}

/**
@template







*/
public function match(&$actual)
{
foreach ($this->_expected as $exp) {
if ($actual === $exp || $actual == $exp) {
return false;
}
}

return true;
}
}
