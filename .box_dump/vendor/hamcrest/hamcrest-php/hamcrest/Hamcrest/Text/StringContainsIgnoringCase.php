<?php
namespace Hamcrest\Text;








class StringContainsIgnoringCase extends SubstringMatcher
{

public function __construct($substring)
{
parent::__construct($substring);
}

/**
@factory


*/
public static function containsStringIgnoringCase($substring)
{
return new self($substring);
}



protected function evalSubstringOf($item)
{
return (false !== stripos((string) $item, $this->_substring));
}

protected function relationship()
{
return 'containing in any case';
}
}
