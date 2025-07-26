<?php
namespace Hamcrest\Internal;




use Hamcrest\Description;
use Hamcrest\SelfDescribing;




class SelfDescribingValue implements SelfDescribing
{

private $_value;

public function __construct($value)
{
$this->_value = $value;
}

public function describeTo(Description $description)
{
$description->appendValue($this->_value);
}
}
