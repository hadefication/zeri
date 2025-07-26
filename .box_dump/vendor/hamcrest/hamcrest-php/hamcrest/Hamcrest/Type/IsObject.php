<?php
namespace Hamcrest\Type;




use Hamcrest\Core\IsTypeOf;




class IsObject extends IsTypeOf
{




public function __construct()
{
parent::__construct('object');
}

/**
@factory


*/
public static function objectValue()
{
return new self;
}
}
