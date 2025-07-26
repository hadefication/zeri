<?php
namespace Hamcrest\Core;





use Hamcrest\Description;
use Hamcrest\Matcher;
use Hamcrest\TypeSafeDiagnosingMatcher;

class Every extends TypeSafeDiagnosingMatcher
{

private $_matcher;

public function __construct(Matcher $matcher)
{
parent::__construct(self::TYPE_ARRAY);

$this->_matcher = $matcher;
}

protected function matchesSafelyWithDiagnosticDescription($items, Description $mismatchDescription)
{
foreach ($items as $item) {
if (!$this->_matcher->matches($item)) {
$mismatchDescription->appendText('an item ');
$this->_matcher->describeMismatch($item, $mismatchDescription);

return false;
}
}

return true;
}

public function describeTo(Description $description)
{
$description->appendText('every item is ')->appendDescriptionOf($this->_matcher);
}

/**
@factory






*/
public static function everyItem(Matcher $itemMatcher)
{
return new self($itemMatcher);
}
}
