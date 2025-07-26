<?php
namespace Hamcrest;








class StringDescription extends BaseDescription
{

private $_out;

public function __construct($out = '')
{
$this->_out = (string) $out;
}

public function __toString()
{
return $this->_out;
}











public static function toString(SelfDescribing $selfDescribing)
{
$self = new self();

return (string) $self->appendDescriptionOf($selfDescribing);
}




public static function asString(SelfDescribing $selfDescribing)
{
return self::toString($selfDescribing);
}



protected function append($str)
{
$this->_out .= $str;
}
}
