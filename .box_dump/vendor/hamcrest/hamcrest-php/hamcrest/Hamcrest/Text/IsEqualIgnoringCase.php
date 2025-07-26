<?php
namespace Hamcrest\Text;




use Hamcrest\Description;
use Hamcrest\TypeSafeMatcher;




class IsEqualIgnoringCase extends TypeSafeMatcher
{

private $_string;

public function __construct($string)
{
parent::__construct(self::TYPE_STRING);

$this->_string = $string;
}

protected function matchesSafely($item)
{
return strtolower($this->_string) === strtolower($item);
}

protected function describeMismatchSafely($item, Description $mismatchDescription)
{
$mismatchDescription->appendText('was ')->appendText($item);
}

public function describeTo(Description $description)
{
$description->appendText('equalToIgnoringCase(')
->appendValue($this->_string)
->appendText(')')
;
}

/**
@factory


*/
public static function equalToIgnoringCase($string)
{
return new self($string);
}
}
