<?php
namespace Hamcrest\Type;




use Hamcrest\Core\IsTypeOf;




class IsArray extends IsTypeOf
{




public function __construct()
{
parent::__construct('array');
}

/**
@factory


*/
public static function arrayValue()
{
return new self;
}
}
