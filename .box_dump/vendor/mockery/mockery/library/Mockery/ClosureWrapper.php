<?php









namespace Mockery;

use Closure;

use function func_get_args;




class ClosureWrapper
{
private $closure;

public function __construct(Closure $closure)
{
$this->closure = $closure;
}




public function __invoke()
{
return ($this->closure)(...func_get_args());
}
}
