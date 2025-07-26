<?php







namespace Hamcrest;




class Matchers
{




public static function anArray()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Arrays\IsArray', 'anArray'), $args);
}








public static function hasItemInArray($item)
{
return \Hamcrest\Arrays\IsArrayContaining::hasItemInArray($item);
}








public static function hasValue($item)
{
return \Hamcrest\Arrays\IsArrayContaining::hasItemInArray($item);
}




public static function arrayContainingInAnyOrder()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Arrays\IsArrayContainingInAnyOrder', 'arrayContainingInAnyOrder'), $args);
}




public static function containsInAnyOrder()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Arrays\IsArrayContainingInAnyOrder', 'arrayContainingInAnyOrder'), $args);
}




public static function arrayContaining()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Arrays\IsArrayContainingInOrder', 'arrayContaining'), $args);
}




public static function contains()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Arrays\IsArrayContainingInOrder', 'arrayContaining'), $args);
}








public static function hasKeyInArray($key)
{
return \Hamcrest\Arrays\IsArrayContainingKey::hasKeyInArray($key);
}








public static function hasKey($key)
{
return \Hamcrest\Arrays\IsArrayContainingKey::hasKeyInArray($key);
}




public static function hasKeyValuePair($key, $value)
{
return \Hamcrest\Arrays\IsArrayContainingKeyValuePair::hasKeyValuePair($key, $value);
}




public static function hasEntry($key, $value)
{
return \Hamcrest\Arrays\IsArrayContainingKeyValuePair::hasKeyValuePair($key, $value);
}








public static function arrayWithSize($size)
{
return \Hamcrest\Arrays\IsArrayWithSize::arrayWithSize($size);
}




public static function emptyArray()
{
return \Hamcrest\Arrays\IsArrayWithSize::emptyArray();
}




public static function nonEmptyArray()
{
return \Hamcrest\Arrays\IsArrayWithSize::nonEmptyArray();
}




public static function emptyTraversable()
{
return \Hamcrest\Collection\IsEmptyTraversable::emptyTraversable();
}




public static function nonEmptyTraversable()
{
return \Hamcrest\Collection\IsEmptyTraversable::nonEmptyTraversable();
}




public static function traversableWithSize($size)
{
return \Hamcrest\Collection\IsTraversableWithSize::traversableWithSize($size);
}




public static function allOf()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Core\AllOf', 'allOf'), $args);
}




public static function anyOf()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Core\AnyOf', 'anyOf'), $args);
}




public static function noneOf()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Core\AnyOf', 'noneOf'), $args);
}








public static function both(\Hamcrest\Matcher $matcher)
{
return \Hamcrest\Core\CombinableMatcher::both($matcher);
}








public static function either(\Hamcrest\Matcher $matcher)
{
return \Hamcrest\Core\CombinableMatcher::either($matcher);
}




public static function describedAs()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Core\DescribedAs', 'describedAs'), $args);
}








public static function everyItem(\Hamcrest\Matcher $itemMatcher)
{
return \Hamcrest\Core\Every::everyItem($itemMatcher);
}




public static function hasToString($matcher)
{
return \Hamcrest\Core\HasToString::hasToString($matcher);
}








public static function is($value)
{
return \Hamcrest\Core\Is::is($value);
}








public static function anything($description = 'ANYTHING')
{
return \Hamcrest\Core\IsAnything::anything($description);
}











public static function hasItem()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Core\IsCollectionContaining', 'hasItem'), $args);
}










public static function hasItems()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Core\IsCollectionContaining', 'hasItems'), $args);
}





public static function equalTo($item)
{
return \Hamcrest\Core\IsEqual::equalTo($item);
}




public static function identicalTo($value)
{
return \Hamcrest\Core\IsIdentical::identicalTo($value);
}







public static function anInstanceOf($theClass)
{
return \Hamcrest\Core\IsInstanceOf::anInstanceOf($theClass);
}







public static function any($theClass)
{
return \Hamcrest\Core\IsInstanceOf::anInstanceOf($theClass);
}




public static function not($value)
{
return \Hamcrest\Core\IsNot::not($value);
}




public static function nullValue()
{
return \Hamcrest\Core\IsNull::nullValue();
}




public static function notNullValue()
{
return \Hamcrest\Core\IsNull::notNullValue();
}










public static function sameInstance($object)
{
return \Hamcrest\Core\IsSame::sameInstance($object);
}




public static function typeOf($theType)
{
return \Hamcrest\Core\IsTypeOf::typeOf($theType);
}




public static function set($property)
{
return \Hamcrest\Core\Set::set($property);
}




public static function notSet($property)
{
return \Hamcrest\Core\Set::notSet($property);
}





public static function closeTo($value, $delta)
{
return \Hamcrest\Number\IsCloseTo::closeTo($value, $delta);
}




public static function comparesEqualTo($value)
{
return \Hamcrest\Number\OrderingComparison::comparesEqualTo($value);
}




public static function greaterThan($value)
{
return \Hamcrest\Number\OrderingComparison::greaterThan($value);
}




public static function greaterThanOrEqualTo($value)
{
return \Hamcrest\Number\OrderingComparison::greaterThanOrEqualTo($value);
}




public static function atLeast($value)
{
return \Hamcrest\Number\OrderingComparison::greaterThanOrEqualTo($value);
}




public static function lessThan($value)
{
return \Hamcrest\Number\OrderingComparison::lessThan($value);
}




public static function lessThanOrEqualTo($value)
{
return \Hamcrest\Number\OrderingComparison::lessThanOrEqualTo($value);
}




public static function atMost($value)
{
return \Hamcrest\Number\OrderingComparison::lessThanOrEqualTo($value);
}




public static function isEmptyString()
{
return \Hamcrest\Text\IsEmptyString::isEmptyString();
}




public static function emptyString()
{
return \Hamcrest\Text\IsEmptyString::isEmptyString();
}




public static function isEmptyOrNullString()
{
return \Hamcrest\Text\IsEmptyString::isEmptyOrNullString();
}




public static function nullOrEmptyString()
{
return \Hamcrest\Text\IsEmptyString::isEmptyOrNullString();
}




public static function isNonEmptyString()
{
return \Hamcrest\Text\IsEmptyString::isNonEmptyString();
}




public static function nonEmptyString()
{
return \Hamcrest\Text\IsEmptyString::isNonEmptyString();
}




public static function equalToIgnoringCase($string)
{
return \Hamcrest\Text\IsEqualIgnoringCase::equalToIgnoringCase($string);
}




public static function equalToIgnoringWhiteSpace($string)
{
return \Hamcrest\Text\IsEqualIgnoringWhiteSpace::equalToIgnoringWhiteSpace($string);
}




public static function matchesPattern($pattern)
{
return \Hamcrest\Text\MatchesPattern::matchesPattern($pattern);
}




public static function containsString($substring)
{
return \Hamcrest\Text\StringContains::containsString($substring);
}




public static function containsStringIgnoringCase($substring)
{
return \Hamcrest\Text\StringContainsIgnoringCase::containsStringIgnoringCase($substring);
}




public static function stringContainsInOrder()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Text\StringContainsInOrder', 'stringContainsInOrder'), $args);
}




public static function endsWith($substring)
{
return \Hamcrest\Text\StringEndsWith::endsWith($substring);
}




public static function startsWith($substring)
{
return \Hamcrest\Text\StringStartsWith::startsWith($substring);
}




public static function arrayValue()
{
return \Hamcrest\Type\IsArray::arrayValue();
}




public static function booleanValue()
{
return \Hamcrest\Type\IsBoolean::booleanValue();
}




public static function boolValue()
{
return \Hamcrest\Type\IsBoolean::booleanValue();
}




public static function callableValue()
{
return \Hamcrest\Type\IsCallable::callableValue();
}




public static function doubleValue()
{
return \Hamcrest\Type\IsDouble::doubleValue();
}




public static function floatValue()
{
return \Hamcrest\Type\IsDouble::doubleValue();
}




public static function integerValue()
{
return \Hamcrest\Type\IsInteger::integerValue();
}




public static function intValue()
{
return \Hamcrest\Type\IsInteger::integerValue();
}




public static function numericValue()
{
return \Hamcrest\Type\IsNumeric::numericValue();
}




public static function objectValue()
{
return \Hamcrest\Type\IsObject::objectValue();
}




public static function anObject()
{
return \Hamcrest\Type\IsObject::objectValue();
}




public static function resourceValue()
{
return \Hamcrest\Type\IsResource::resourceValue();
}




public static function scalarValue()
{
return \Hamcrest\Type\IsScalar::scalarValue();
}




public static function stringValue()
{
return \Hamcrest\Type\IsString::stringValue();
}






public static function hasXPath($xpath, $matcher = null)
{
return \Hamcrest\Xml\HasXPath::hasXPath($xpath, $matcher);
}
}
