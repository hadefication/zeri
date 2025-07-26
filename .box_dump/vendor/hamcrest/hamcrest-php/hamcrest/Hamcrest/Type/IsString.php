<?php
namespace Hamcrest\Type;




use Hamcrest\Core\IsTypeOf;




class IsString extends IsTypeOf
{




public function __construct()
{
parent::__construct('string');
}

/**
@factory


*/
public static function stringValue()
{
return new self;
}
}
