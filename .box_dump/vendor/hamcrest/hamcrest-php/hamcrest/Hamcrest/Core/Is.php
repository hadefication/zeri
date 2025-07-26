<?php
namespace Hamcrest\Core;




use Hamcrest\BaseMatcher;
use Hamcrest\Description;
use Hamcrest\Matcher;
use Hamcrest\Util;








class Is extends BaseMatcher
{

private $_matcher;

public function __construct(Matcher $matcher)
{
$this->_matcher = $matcher;
}

public function matches($arg)
{
return $this->_matcher->matches($arg);
}

public function describeTo(Description $description)
{
$description->appendText('is ')->appendDescriptionOf($this->_matcher);
}

public function describeMismatch($item, Description $mismatchDescription)
{
$this->_matcher->describeMismatch($item, $mismatchDescription);
}

/**
@factory






*/
public static function is($value)
{
return new self(Util::wrapValueWithIsEqual($value));
}
}
