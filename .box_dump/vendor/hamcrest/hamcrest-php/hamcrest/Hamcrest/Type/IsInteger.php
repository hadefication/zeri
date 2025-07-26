<?php
namespace Hamcrest\Type;




use Hamcrest\Core\IsTypeOf;




class IsInteger extends IsTypeOf
{




public function __construct()
{
parent::__construct('integer');
}

/**
@factory


*/
public static function integerValue()
{
return new self;
}
}
