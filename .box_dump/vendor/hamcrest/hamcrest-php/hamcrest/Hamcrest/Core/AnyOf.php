<?php
namespace Hamcrest\Core;




use Hamcrest\Description;
use Hamcrest\Util;






class AnyOf extends ShortcutCombination
{

public function __construct(array $matchers)
{
parent::__construct($matchers);
}

public function matches($item)
{
return $this->matchesWithShortcut($item, true);
}

public function describeTo(Description $description)
{
$this->describeToWithOperator($description, 'or');
}

/**
@factory


*/
public static function anyOf()
{
$args = func_get_args();

return new self(Util::createMatcherArray($args));
}

/**
@factory


*/
public static function noneOf()
{
$args = func_get_args();

return IsNot::not(
new self(Util::createMatcherArray($args))
);
}
}
