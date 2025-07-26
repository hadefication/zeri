<?php
namespace Hamcrest\Text;








class MatchesPattern extends SubstringMatcher
{

public function __construct($pattern)
{
parent::__construct($pattern);
}

/**
@factory


*/
public static function matchesPattern($pattern)
{
return new self($pattern);
}



protected function evalSubstringOf($item)
{
return preg_match($this->_substring, (string) $item) >= 1;
}

protected function relationship()
{
return 'matching';
}
}
