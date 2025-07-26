<?php
namespace Hamcrest\Core;




use Hamcrest\Description;
use Hamcrest\Matcher;
use Hamcrest\TypeSafeMatcher;
use Hamcrest\Util;




class IsCollectionContaining extends TypeSafeMatcher
{

private $_elementMatcher;

public function __construct(Matcher $elementMatcher)
{
parent::__construct(self::TYPE_ARRAY);

$this->_elementMatcher = $elementMatcher;
}

protected function matchesSafely($items)
{
foreach ($items as $item) {
if ($this->_elementMatcher->matches($item)) {
return true;
}
}

return false;
}

protected function describeMismatchSafely($items, Description $mismatchDescription)
{
$mismatchDescription->appendText('was ')->appendValue($items);
}

public function describeTo(Description $description)
{
$description
->appendText('a collection containing ')
->appendDescriptionOf($this->_elementMatcher)
;
}

/**
@factory









*/
public static function hasItem()
{
$args = func_get_args();
$firstArg = array_shift($args);

return new self(Util::wrapValueWithIsEqual($firstArg));
}

/**
@factory








*/
public static function hasItems()
{
$args = func_get_args();
$matchers = array();

foreach ($args as $arg) {
$matchers[] = self::hasItem($arg);
}

return AllOf::allOf($matchers);
}
}
