<?php
namespace Hamcrest;










class Util
{
public static function registerGlobalFunctions()
{
require_once __DIR__.'/../Hamcrest.php';
}







public static function wrapValueWithIsEqual($item)
{
return ($item instanceof Matcher)
? $item
: Core\IsEqual::equalTo($item)
;
}







public static function checkAllAreMatchers(array $matchers)
{
foreach ($matchers as $m) {
if (!($m instanceof Matcher)) {
throw new \InvalidArgumentException(
'Each argument or element must be a Hamcrest matcher'
);
}
}
}










public static function createMatcherArray(array $items)
{

if (count($items) == 1 && is_array($items[0])) {
$items = $items[0];
}


foreach ($items as &$item) {
if (!($item instanceof Matcher)) {
$item = Core\IsEqual::equalTo($item);
}
}

return $items;
}
}
