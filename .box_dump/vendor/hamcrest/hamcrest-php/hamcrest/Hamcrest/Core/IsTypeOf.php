<?php
namespace Hamcrest\Core;




use Hamcrest\BaseMatcher;
use Hamcrest\Description;




class IsTypeOf extends BaseMatcher
{

private $_theType;







public function __construct($theType)
{
$this->_theType = strtolower($theType);
}

public function matches($item)
{
return strtolower(gettype($item)) == $this->_theType;
}

public function describeTo(Description $description)
{
$description->appendText(self::getTypeDescription($this->_theType));
}

public function describeMismatch($item, Description $description)
{
if ($item === null) {
$description->appendText('was null');
} else {
$description->appendText('was ')
->appendText(self::getTypeDescription(strtolower(gettype($item))))
->appendText(' ')
->appendValue($item)
;
}
}

public static function getTypeDescription($type)
{
if ($type == 'null') {
return 'null';
}

return (strpos('aeiou', substr($type, 0, 1)) === false ? 'a ' : 'an ')
. $type;
}

/**
@factory


*/
public static function typeOf($theType)
{
return new self($theType);
}
}
