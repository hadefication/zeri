<?php
namespace Hamcrest\Type;




use Hamcrest\Core\IsTypeOf;




class IsScalar extends IsTypeOf
{

public function __construct()
{
parent::__construct('scalar');
}

public function matches($item)
{
return is_scalar($item);
}

/**
@factory


*/
public static function scalarValue()
{
return new self;
}
}
