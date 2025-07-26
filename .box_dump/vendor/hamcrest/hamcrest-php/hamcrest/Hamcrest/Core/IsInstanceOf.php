<?php
namespace Hamcrest\Core;




use Hamcrest\Description;
use Hamcrest\DiagnosingMatcher;




class IsInstanceOf extends DiagnosingMatcher
{

private $_theClass;








public function __construct($theClass)
{
$this->_theClass = $theClass;
}

protected function matchesWithDiagnosticDescription($item, Description $mismatchDescription)
{
if (!is_object($item)) {
$mismatchDescription->appendText('was ')->appendValue($item);

return false;
}

if (!($item instanceof $this->_theClass)) {
$mismatchDescription->appendText('[' . get_class($item) . '] ')
->appendValue($item);

return false;
}

return true;
}

public function describeTo(Description $description)
{
$description->appendText('an instance of ')
->appendText($this->_theClass)
;
}

/**
@factory





*/
public static function anInstanceOf($theClass)
{
return new self($theClass);
}
}
