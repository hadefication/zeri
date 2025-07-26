<?php







if (!function_exists('assertThat')) {













function assertThat()
{
$args = func_get_args();
call_user_func_array(
array('Hamcrest\MatcherAssert', 'assertThat'),
$args
);
}
}

if (!function_exists('anArray')) {



function anArray()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Arrays\IsArray', 'anArray'), $args);
}
}

if (!function_exists('hasItemInArray')) {







function hasItemInArray($item)
{
return \Hamcrest\Arrays\IsArrayContaining::hasItemInArray($item);
}
}

if (!function_exists('hasValue')) {







function hasValue($item)
{
return \Hamcrest\Arrays\IsArrayContaining::hasItemInArray($item);
}
}

if (!function_exists('arrayContainingInAnyOrder')) {



function arrayContainingInAnyOrder()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Arrays\IsArrayContainingInAnyOrder', 'arrayContainingInAnyOrder'), $args);
}
}

if (!function_exists('containsInAnyOrder')) {



function containsInAnyOrder()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Arrays\IsArrayContainingInAnyOrder', 'arrayContainingInAnyOrder'), $args);
}
}

if (!function_exists('arrayContaining')) {



function arrayContaining()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Arrays\IsArrayContainingInOrder', 'arrayContaining'), $args);
}
}

if (!function_exists('contains')) {



function contains()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Arrays\IsArrayContainingInOrder', 'arrayContaining'), $args);
}
}

if (!function_exists('hasKeyInArray')) {







function hasKeyInArray($key)
{
return \Hamcrest\Arrays\IsArrayContainingKey::hasKeyInArray($key);
}
}

if (!function_exists('hasKey')) {







function hasKey($key)
{
return \Hamcrest\Arrays\IsArrayContainingKey::hasKeyInArray($key);
}
}

if (!function_exists('hasKeyValuePair')) {



function hasKeyValuePair($key, $value)
{
return \Hamcrest\Arrays\IsArrayContainingKeyValuePair::hasKeyValuePair($key, $value);
}
}

if (!function_exists('hasEntry')) {



function hasEntry($key, $value)
{
return \Hamcrest\Arrays\IsArrayContainingKeyValuePair::hasKeyValuePair($key, $value);
}
}

if (!function_exists('arrayWithSize')) {







function arrayWithSize($size)
{
return \Hamcrest\Arrays\IsArrayWithSize::arrayWithSize($size);
}
}

if (!function_exists('emptyArray')) {



function emptyArray()
{
return \Hamcrest\Arrays\IsArrayWithSize::emptyArray();
}
}

if (!function_exists('nonEmptyArray')) {



function nonEmptyArray()
{
return \Hamcrest\Arrays\IsArrayWithSize::nonEmptyArray();
}
}

if (!function_exists('emptyTraversable')) {



function emptyTraversable()
{
return \Hamcrest\Collection\IsEmptyTraversable::emptyTraversable();
}
}

if (!function_exists('nonEmptyTraversable')) {



function nonEmptyTraversable()
{
return \Hamcrest\Collection\IsEmptyTraversable::nonEmptyTraversable();
}
}

if (!function_exists('traversableWithSize')) {



function traversableWithSize($size)
{
return \Hamcrest\Collection\IsTraversableWithSize::traversableWithSize($size);
}
}

if (!function_exists('allOf')) {



function allOf()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Core\AllOf', 'allOf'), $args);
}
}

if (!function_exists('anyOf')) {



function anyOf()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Core\AnyOf', 'anyOf'), $args);
}
}

if (!function_exists('noneOf')) {



function noneOf()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Core\AnyOf', 'noneOf'), $args);
}
}

if (!function_exists('both')) {







function both(\Hamcrest\Matcher $matcher)
{
return \Hamcrest\Core\CombinableMatcher::both($matcher);
}
}

if (!function_exists('either')) {







function either(\Hamcrest\Matcher $matcher)
{
return \Hamcrest\Core\CombinableMatcher::either($matcher);
}
}

if (!function_exists('describedAs')) {



function describedAs()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Core\DescribedAs', 'describedAs'), $args);
}
}

if (!function_exists('everyItem')) {







function everyItem(\Hamcrest\Matcher $itemMatcher)
{
return \Hamcrest\Core\Every::everyItem($itemMatcher);
}
}

if (!function_exists('hasToString')) {



function hasToString($matcher)
{
return \Hamcrest\Core\HasToString::hasToString($matcher);
}
}

if (!function_exists('is')) {







function is($value)
{
return \Hamcrest\Core\Is::is($value);
}
}

if (!function_exists('anything')) {







function anything($description = 'ANYTHING')
{
return \Hamcrest\Core\IsAnything::anything($description);
}
}

if (!function_exists('hasItem')) {










function hasItem()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Core\IsCollectionContaining', 'hasItem'), $args);
}
}

if (!function_exists('hasItems')) {









function hasItems()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Core\IsCollectionContaining', 'hasItems'), $args);
}
}

if (!function_exists('equalTo')) {




function equalTo($item)
{
return \Hamcrest\Core\IsEqual::equalTo($item);
}
}

if (!function_exists('identicalTo')) {



function identicalTo($value)
{
return \Hamcrest\Core\IsIdentical::identicalTo($value);
}
}

if (!function_exists('anInstanceOf')) {






function anInstanceOf($theClass)
{
return \Hamcrest\Core\IsInstanceOf::anInstanceOf($theClass);
}
}

if (!function_exists('any')) {






function any($theClass)
{
return \Hamcrest\Core\IsInstanceOf::anInstanceOf($theClass);
}
}

if (!function_exists('not')) {



function not($value)
{
return \Hamcrest\Core\IsNot::not($value);
}
}

if (!function_exists('nullValue')) {



function nullValue()
{
return \Hamcrest\Core\IsNull::nullValue();
}
}

if (!function_exists('notNullValue')) {



function notNullValue()
{
return \Hamcrest\Core\IsNull::notNullValue();
}
}

if (!function_exists('sameInstance')) {









function sameInstance($object)
{
return \Hamcrest\Core\IsSame::sameInstance($object);
}
}

if (!function_exists('typeOf')) {



function typeOf($theType)
{
return \Hamcrest\Core\IsTypeOf::typeOf($theType);
}
}

if (!function_exists('set')) {



function set($property)
{
return \Hamcrest\Core\Set::set($property);
}
}

if (!function_exists('notSet')) {



function notSet($property)
{
return \Hamcrest\Core\Set::notSet($property);
}
}

if (!function_exists('closeTo')) {




function closeTo($value, $delta)
{
return \Hamcrest\Number\IsCloseTo::closeTo($value, $delta);
}
}

if (!function_exists('comparesEqualTo')) {



function comparesEqualTo($value)
{
return \Hamcrest\Number\OrderingComparison::comparesEqualTo($value);
}
}

if (!function_exists('greaterThan')) {



function greaterThan($value)
{
return \Hamcrest\Number\OrderingComparison::greaterThan($value);
}
}

if (!function_exists('greaterThanOrEqualTo')) {



function greaterThanOrEqualTo($value)
{
return \Hamcrest\Number\OrderingComparison::greaterThanOrEqualTo($value);
}
}

if (!function_exists('atLeast')) {



function atLeast($value)
{
return \Hamcrest\Number\OrderingComparison::greaterThanOrEqualTo($value);
}
}

if (!function_exists('lessThan')) {



function lessThan($value)
{
return \Hamcrest\Number\OrderingComparison::lessThan($value);
}
}

if (!function_exists('lessThanOrEqualTo')) {



function lessThanOrEqualTo($value)
{
return \Hamcrest\Number\OrderingComparison::lessThanOrEqualTo($value);
}
}

if (!function_exists('atMost')) {



function atMost($value)
{
return \Hamcrest\Number\OrderingComparison::lessThanOrEqualTo($value);
}
}

if (!function_exists('isEmptyString')) {



function isEmptyString()
{
return \Hamcrest\Text\IsEmptyString::isEmptyString();
}
}

if (!function_exists('emptyString')) {



function emptyString()
{
return \Hamcrest\Text\IsEmptyString::isEmptyString();
}
}

if (!function_exists('isEmptyOrNullString')) {



function isEmptyOrNullString()
{
return \Hamcrest\Text\IsEmptyString::isEmptyOrNullString();
}
}

if (!function_exists('nullOrEmptyString')) {



function nullOrEmptyString()
{
return \Hamcrest\Text\IsEmptyString::isEmptyOrNullString();
}
}

if (!function_exists('isNonEmptyString')) {



function isNonEmptyString()
{
return \Hamcrest\Text\IsEmptyString::isNonEmptyString();
}
}

if (!function_exists('nonEmptyString')) {



function nonEmptyString()
{
return \Hamcrest\Text\IsEmptyString::isNonEmptyString();
}
}

if (!function_exists('equalToIgnoringCase')) {



function equalToIgnoringCase($string)
{
return \Hamcrest\Text\IsEqualIgnoringCase::equalToIgnoringCase($string);
}
}

if (!function_exists('equalToIgnoringWhiteSpace')) {



function equalToIgnoringWhiteSpace($string)
{
return \Hamcrest\Text\IsEqualIgnoringWhiteSpace::equalToIgnoringWhiteSpace($string);
}
}

if (!function_exists('matchesPattern')) {



function matchesPattern($pattern)
{
return \Hamcrest\Text\MatchesPattern::matchesPattern($pattern);
}
}

if (!function_exists('containsString')) {



function containsString($substring)
{
return \Hamcrest\Text\StringContains::containsString($substring);
}
}

if (!function_exists('containsStringIgnoringCase')) {



function containsStringIgnoringCase($substring)
{
return \Hamcrest\Text\StringContainsIgnoringCase::containsStringIgnoringCase($substring);
}
}

if (!function_exists('stringContainsInOrder')) {



function stringContainsInOrder()
{
$args = func_get_args();
return call_user_func_array(array('\Hamcrest\Text\StringContainsInOrder', 'stringContainsInOrder'), $args);
}
}

if (!function_exists('endsWith')) {



function endsWith($substring)
{
return \Hamcrest\Text\StringEndsWith::endsWith($substring);
}
}

if (!function_exists('startsWith')) {



function startsWith($substring)
{
return \Hamcrest\Text\StringStartsWith::startsWith($substring);
}
}

if (!function_exists('arrayValue')) {



function arrayValue()
{
return \Hamcrest\Type\IsArray::arrayValue();
}
}

if (!function_exists('booleanValue')) {



function booleanValue()
{
return \Hamcrest\Type\IsBoolean::booleanValue();
}
}

if (!function_exists('boolValue')) {



function boolValue()
{
return \Hamcrest\Type\IsBoolean::booleanValue();
}
}

if (!function_exists('callableValue')) {



function callableValue()
{
return \Hamcrest\Type\IsCallable::callableValue();
}
}

if (!function_exists('doubleValue')) {



function doubleValue()
{
return \Hamcrest\Type\IsDouble::doubleValue();
}
}

if (!function_exists('floatValue')) {



function floatValue()
{
return \Hamcrest\Type\IsDouble::doubleValue();
}
}

if (!function_exists('integerValue')) {



function integerValue()
{
return \Hamcrest\Type\IsInteger::integerValue();
}
}

if (!function_exists('intValue')) {



function intValue()
{
return \Hamcrest\Type\IsInteger::integerValue();
}
}

if (!function_exists('numericValue')) {



function numericValue()
{
return \Hamcrest\Type\IsNumeric::numericValue();
}
}

if (!function_exists('objectValue')) {



function objectValue()
{
return \Hamcrest\Type\IsObject::objectValue();
}
}

if (!function_exists('anObject')) {



function anObject()
{
return \Hamcrest\Type\IsObject::objectValue();
}
}

if (!function_exists('resourceValue')) {



function resourceValue()
{
return \Hamcrest\Type\IsResource::resourceValue();
}
}

if (!function_exists('scalarValue')) {



function scalarValue()
{
return \Hamcrest\Type\IsScalar::scalarValue();
}
}

if (!function_exists('stringValue')) {



function stringValue()
{
return \Hamcrest\Type\IsString::stringValue();
}
}

if (!function_exists('hasXPath')) {





function hasXPath($xpath, $matcher = null)
{
return \Hamcrest\Xml\HasXPath::hasXPath($xpath, $matcher);
}
}
