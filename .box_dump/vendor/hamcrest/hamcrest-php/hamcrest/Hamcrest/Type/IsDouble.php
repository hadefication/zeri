<?php
namespace Hamcrest\Type;




use Hamcrest\Core\IsTypeOf;






class IsDouble extends IsTypeOf
{




public function __construct()
{
parent::__construct('double');
}

/**
@factory


*/
public static function doubleValue()
{
return new self;
}
}
