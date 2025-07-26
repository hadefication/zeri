<?php
namespace Hamcrest\Text;








class StringStartsWith extends SubstringMatcher
{

public function __construct($substring)
{
parent::__construct($substring);
}

/**
@factory


*/
public static function startsWith($substring)
{
return new self($substring);
}



protected function evalSubstringOf($string)
{
return (substr($string, 0, strlen($this->_substring)) === $this->_substring);
}

protected function relationship()
{
return 'starting with';
}
}
