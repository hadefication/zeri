<?php
namespace Hamcrest;





class MatcherAssert
{






private static $_count = 0;





















public static function assertThat()
{
$args = func_get_args();
switch (count($args)) {
case 1:
self::$_count++;
if (!$args[0]) {
throw new AssertionError();
}
break;

case 2:
self::$_count++;
if ($args[1] instanceof Matcher) {
self::doAssert('', $args[0], $args[1]);
} elseif (!$args[1]) {
throw new AssertionError($args[0]);
}
break;

case 3:
self::$_count++;
self::doAssert(
$args[0],
$args[1],
Util::wrapValueWithIsEqual($args[2])
);
break;

default:
throw new \InvalidArgumentException('assertThat() requires one to three arguments');
}
}






public static function getCount()
{
return self::$_count;
}




public static function resetCount()
{
self::$_count = 0;
}













private static function doAssert($identifier, $actual, Matcher $matcher)
{
if (!$matcher->matches($actual)) {
$description = new StringDescription();
if (!empty($identifier)) {
$description->appendText($identifier . PHP_EOL);
}
$description->appendText('Expected: ')
->appendDescriptionOf($matcher)
->appendText(PHP_EOL . '     but: ');

$matcher->describeMismatch($actual, $description);

throw new AssertionError((string) $description);
}
}
}
