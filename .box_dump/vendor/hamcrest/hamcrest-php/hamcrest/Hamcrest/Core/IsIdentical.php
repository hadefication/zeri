<?php
namespace Hamcrest\Core;




use Hamcrest\Description;





class IsIdentical extends IsSame
{

private $_value;

public function __construct($value)
{
parent::__construct($value);
$this->_value = $value;
}

public function describeTo(Description $description)
{
$description->appendValue($this->_value);
}

/**
@factory


*/
public static function identicalTo($value)
{
return new self($value);
}
}
