<?php
namespace Hamcrest\Core;




use Hamcrest\BaseMatcher;
use Hamcrest\Description;




class IsAnything extends BaseMatcher
{

private $_message;

public function __construct($message = 'ANYTHING')
{
$this->_message = $message;
}

public function matches($item)
{
return true;
}

public function describeTo(Description $description)
{
$description->appendText($this->_message);
}

/**
@factory





*/
public static function anything($description = 'ANYTHING')
{
return new self($description);
}
}
