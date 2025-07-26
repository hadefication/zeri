<?php

declare(strict_types=1);

namespace Pest\Support;

use Closure;




final class ExpectationPipeline
{





private array $pipes = [];






private array $passables;




public function __construct(
private readonly Closure $closure
) {}




public static function for(Closure $closure): self
{
return new self($closure);
}




public function send(mixed ...$passables): self
{
$this->passables = $passables;

return $this;
}






public function through(array $pipes): self
{
$this->pipes = $pipes;

return $this;
}




public function run(): void
{
$pipeline = array_reduce(
array_reverse($this->pipes),
$this->carry(),
function (): void {
call_user_func_array($this->closure, $this->passables);
}
);

$pipeline();
}




public function carry(): Closure
{
return fn (mixed $stack, callable $pipe): Closure => fn () => $pipe($stack, ...$this->passables);
}
}
