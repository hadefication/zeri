<?php
namespace Hamcrest;










abstract class TypeSafeMatcher extends BaseMatcher
{


const TYPE_ANY = 0;
const TYPE_STRING = 1;
const TYPE_NUMERIC = 2;
const TYPE_ARRAY = 3;
const TYPE_OBJECT = 4;
const TYPE_RESOURCE = 5;
const TYPE_BOOLEAN = 6;






private $_expectedType;






private $_expectedSubtype;

public function __construct($expectedType, $expectedSubtype = null)
{
$this->_expectedType = $expectedType;
$this->_expectedSubtype = $expectedSubtype;
}

final public function matches($item)
{
return $this->_isSafeType($item) && $this->matchesSafely($item);
}

final public function describeMismatch($item, Description $mismatchDescription)
{
if (!$this->_isSafeType($item)) {
parent::describeMismatch($item, $mismatchDescription);
} else {
$this->describeMismatchSafely($item, $mismatchDescription);
}
}






abstract protected function matchesSafely($item);




abstract protected function describeMismatchSafely($item, Description $mismatchDescription);



private function _isSafeType($value)
{
switch ($this->_expectedType) {

case self::TYPE_ANY:
return true;

case self::TYPE_STRING:
return is_string($value) || is_numeric($value);

case self::TYPE_NUMERIC:
return is_numeric($value) || is_string($value);

case self::TYPE_ARRAY:
return is_array($value);

case self::TYPE_OBJECT:
return is_object($value)
&& ($this->_expectedSubtype === null
|| $value instanceof $this->_expectedSubtype);

case self::TYPE_RESOURCE:
return is_resource($value)
&& ($this->_expectedSubtype === null
|| get_resource_type($value) == $this->_expectedSubtype);

case self::TYPE_BOOLEAN:
return true;

default:
return true;

}
}
}
