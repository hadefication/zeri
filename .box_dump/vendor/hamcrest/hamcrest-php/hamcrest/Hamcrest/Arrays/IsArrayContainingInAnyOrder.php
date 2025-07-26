<?php
namespace Hamcrest\Arrays;




use Hamcrest\Description;
use Hamcrest\TypeSafeDiagnosingMatcher;
use Hamcrest\Util;




class IsArrayContainingInAnyOrder extends TypeSafeDiagnosingMatcher
{

private $_elementMatchers;

public function __construct(array $elementMatchers)
{
parent::__construct(self::TYPE_ARRAY);

Util::checkAllAreMatchers($elementMatchers);

$this->_elementMatchers = $elementMatchers;
}

protected function matchesSafelyWithDiagnosticDescription($array, Description $mismatchDescription)
{
$matching = new MatchingOnce($this->_elementMatchers, $mismatchDescription);

foreach ($array as $element) {
if (!$matching->matches($element)) {
return false;
}
}

return $matching->isFinished($array);
}

public function describeTo(Description $description)
{
$description->appendList('[', ', ', ']', $this->_elementMatchers)
->appendText(' in any order')
;
}

/**
@factory


*/
public static function arrayContainingInAnyOrder()
{
$args = func_get_args();

return new self(Util::createMatcherArray($args));
}
}
