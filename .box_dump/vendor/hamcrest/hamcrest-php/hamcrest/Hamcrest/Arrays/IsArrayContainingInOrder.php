<?php
namespace Hamcrest\Arrays;




use Hamcrest\Description;
use Hamcrest\TypeSafeDiagnosingMatcher;
use Hamcrest\Util;




class IsArrayContainingInOrder extends TypeSafeDiagnosingMatcher
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
$series = new SeriesMatchingOnce($this->_elementMatchers, $mismatchDescription);

foreach ($array as $element) {
if (!$series->matches($element)) {
return false;
}
}

return $series->isFinished();
}

public function describeTo(Description $description)
{
$description->appendList('[', ', ', ']', $this->_elementMatchers);
}

/**
@factory


*/
public static function arrayContaining()
{
$args = func_get_args();

return new self(Util::createMatcherArray($args));
}
}
