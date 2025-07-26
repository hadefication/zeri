<?php
namespace Hamcrest\Core;




use Hamcrest\BaseMatcher;
use Hamcrest\Description;





class IsEqual extends BaseMatcher
{

private $_item;

public function __construct($item)
{
$this->_item = $item;
}

public function matches($arg)
{
return (($arg == $this->_item) && ($this->_item == $arg));
}

public function describeTo(Description $description)
{
$description->appendValue($this->_item);
}

/**
@factory



*/
public static function equalTo($item)
{
return new self($item);
}
}
