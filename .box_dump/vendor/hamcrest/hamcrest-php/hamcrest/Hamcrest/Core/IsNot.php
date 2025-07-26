<?php
namespace Hamcrest\Core;




use Hamcrest\BaseMatcher;
use Hamcrest\Description;
use Hamcrest\Matcher;
use Hamcrest\Util;




class IsNot extends BaseMatcher
{

private $_matcher;

public function __construct(Matcher $matcher)
{
$this->_matcher = $matcher;
}

public function matches($arg)
{
return !$this->_matcher->matches($arg);
}

public function describeTo(Description $description)
{
$description->appendText('not ')->appendDescriptionOf($this->_matcher);
}

/**
@factory


*/
public static function not($value)
{
return new self(Util::wrapValueWithIsEqual($value));
}
}
