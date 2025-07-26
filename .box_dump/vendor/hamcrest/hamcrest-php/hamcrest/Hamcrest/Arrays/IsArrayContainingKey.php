<?php
namespace Hamcrest\Arrays;




use Hamcrest\Description;
use Hamcrest\Matcher;
use Hamcrest\TypeSafeMatcher;
use Hamcrest\Util;




class IsArrayContainingKey extends TypeSafeMatcher
{

private $_keyMatcher;

public function __construct(Matcher $keyMatcher)
{
parent::__construct(self::TYPE_ARRAY);

$this->_keyMatcher = $keyMatcher;
}

protected function matchesSafely($array)
{
foreach ($array as $key => $element) {
if ($this->_keyMatcher->matches($key)) {
return true;
}
}

return false;
}

protected function describeMismatchSafely($array, Description $mismatchDescription)
{

$mismatchDescription->appendText('array was ')
->appendText('[')
;
$loop = false;
foreach ($array as $key => $value) {
if ($loop) {
$mismatchDescription->appendText(', ');
}
$mismatchDescription->appendValue($key)->appendText(' => ')->appendValue($value);
$loop = true;
}
$mismatchDescription->appendText(']');
}

public function describeTo(Description $description)
{
$description
->appendText('array with key ')
->appendDescriptionOf($this->_keyMatcher)
;
}

/**
@factory





*/
public static function hasKeyInArray($key)
{
return new self(Util::wrapValueWithIsEqual($key));
}
}
