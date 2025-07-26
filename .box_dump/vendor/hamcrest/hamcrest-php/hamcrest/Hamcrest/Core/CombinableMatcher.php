<?php
namespace Hamcrest\Core;





use Hamcrest\BaseMatcher;
use Hamcrest\Description;
use Hamcrest\Matcher;

class CombinableMatcher extends BaseMatcher
{

private $_matcher;

public function __construct(Matcher $matcher)
{
$this->_matcher = $matcher;
}

public function matches($item)
{
return $this->_matcher->matches($item);
}

public function describeTo(Description $description)
{
$description->appendDescriptionOf($this->_matcher);
}


public function andAlso(Matcher $other)
{
return new self(new AllOf($this->_templatedListWith($other)));
}


public function orElse(Matcher $other)
{
return new self(new AnyOf($this->_templatedListWith($other)));
}

/**
@factory






*/
public static function both(Matcher $matcher)
{
return new self($matcher);
}

/**
@factory






*/
public static function either(Matcher $matcher)
{
return new self($matcher);
}



private function _templatedListWith(Matcher $other)
{
return array($this->_matcher, $other);
}
}
