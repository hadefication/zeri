<?php
namespace Hamcrest\Type;




use Hamcrest\Core\IsTypeOf;




class IsResource extends IsTypeOf
{




public function __construct()
{
parent::__construct('resource');
}

/**
@factory


*/
public static function resourceValue()
{
return new self;
}
}
