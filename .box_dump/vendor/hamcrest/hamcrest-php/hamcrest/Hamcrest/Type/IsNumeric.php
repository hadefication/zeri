<?php
namespace Hamcrest\Type;




use Hamcrest\Core\IsTypeOf;




class IsNumeric extends IsTypeOf
{

public function __construct()
{
parent::__construct('number');
}

public function matches($item)
{
if ($this->isHexadecimal($item)) {
return true;
}

return is_numeric($item);
}








private function isHexadecimal($item)
{
if (is_string($item) && preg_match('/^0x(.*)$/', $item, $matches)) {
return ctype_xdigit($matches[1]);
}

return false;
}

/**
@factory


*/
public static function numericValue()
{
return new self;
}
}
