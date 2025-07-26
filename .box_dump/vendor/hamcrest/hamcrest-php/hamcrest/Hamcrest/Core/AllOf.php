<?php
namespace Hamcrest\Core;




use Hamcrest\Description;
use Hamcrest\DiagnosingMatcher;
use Hamcrest\Util;






class AllOf extends DiagnosingMatcher
{

private $_matchers;

public function __construct(array $matchers)
{
Util::checkAllAreMatchers($matchers);

$this->_matchers = $matchers;
}

public function matchesWithDiagnosticDescription($item, Description $mismatchDescription)
{
/**
@@var $matcher \Hamcrest\Matcher */
foreach ($this->_matchers as $matcher) {
if (!$matcher->matches($item)) {
$mismatchDescription->appendDescriptionOf($matcher)->appendText(' ');
$matcher->describeMismatch($item, $mismatchDescription);

return false;
}
}

return true;
}

public function describeTo(Description $description)
{
$description->appendList('(', ' and ', ')', $this->_matchers);
}

/**
@factory


*/
public static function allOf()
{
$args = func_get_args();

return new self(Util::createMatcherArray($args));
}
}
