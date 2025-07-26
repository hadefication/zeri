<?php
namespace Hamcrest\Type;




use Hamcrest\Core\IsTypeOf;




class IsBoolean extends IsTypeOf
{




public function __construct()
{
parent::__construct('boolean');
}

/**
@factory


*/
public static function booleanValue()
{
return new self;
}
}
