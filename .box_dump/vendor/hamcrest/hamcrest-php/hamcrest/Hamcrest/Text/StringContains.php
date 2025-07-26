<?php
namespace Hamcrest\Text;








class StringContains extends SubstringMatcher
{

public function __construct($substring)
{
parent::__construct($substring);
}

public function ignoringCase()
{
return new StringContainsIgnoringCase($this->_substring);
}

/**
@factory


*/
public static function containsString($substring)
{
return new self($substring);
}



protected function evalSubstringOf($item)
{
return (false !== strpos((string) $item, $this->_substring));
}

protected function relationship()
{
return 'containing';
}
}
