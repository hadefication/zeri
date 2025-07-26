<?php
namespace Hamcrest\Text;




use Hamcrest\Description;
use Hamcrest\TypeSafeMatcher;





class IsEqualIgnoringWhiteSpace extends TypeSafeMatcher
{

private $_string;

public function __construct($string)
{
parent::__construct(self::TYPE_STRING);

$this->_string = $string;
}

protected function matchesSafely($item)
{
return (strtolower($this->_stripSpace($item))
=== strtolower($this->_stripSpace($this->_string)));
}

protected function describeMismatchSafely($item, Description $mismatchDescription)
{
$mismatchDescription->appendText('was ')->appendText($item);
}

public function describeTo(Description $description)
{
$description->appendText('equalToIgnoringWhiteSpace(')
->appendValue($this->_string)
->appendText(')')
;
}

/**
@factory


*/
public static function equalToIgnoringWhiteSpace($string)
{
return new self($string);
}



private function _stripSpace($string)
{
$parts = preg_split("/[\r\n\t ]+/", $string);
foreach ($parts as $i => $part) {
$parts[$i] = trim($part, " \r\n\t");
}

return trim(implode(' ', $parts), " \r\n\t");
}
}
