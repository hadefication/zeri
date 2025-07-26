<?php
namespace Hamcrest\Core;




use Hamcrest\BaseMatcher;
use Hamcrest\Description;





class IsSame extends BaseMatcher
{

private $_object;

public function __construct($object)
{
$this->_object = $object;
}

public function matches($object)
{
return ($object === $this->_object) && ($this->_object === $object);
}

public function describeTo(Description $description)
{
$description->appendText('sameInstance(')
->appendValue($this->_object)
->appendText(')')
;
}

/**
@factory







*/
public static function sameInstance($object)
{
return new self($object);
}
}
