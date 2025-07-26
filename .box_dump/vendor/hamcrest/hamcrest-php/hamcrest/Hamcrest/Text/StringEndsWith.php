<?php
namespace Hamcrest\Text;








class StringEndsWith extends SubstringMatcher
{

public function __construct($substring)
{
parent::__construct($substring);
}

/**
@factory


*/
public static function endsWith($substring)
{
return new self($substring);
}



protected function evalSubstringOf($string)
{
return (substr($string, (-1 * strlen($this->_substring))) === $this->_substring);
}

protected function relationship()
{
return 'ending with';
}
}
