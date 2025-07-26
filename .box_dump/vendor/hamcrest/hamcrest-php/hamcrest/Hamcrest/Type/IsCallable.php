<?php
namespace Hamcrest\Type;




use Hamcrest\Core\IsTypeOf;




class IsCallable extends IsTypeOf
{




public function __construct()
{
parent::__construct('callable');
}

public function matches($item)
{
return is_callable($item);
}

/**
@factory


*/
public static function callableValue()
{
return new self;
}
}
