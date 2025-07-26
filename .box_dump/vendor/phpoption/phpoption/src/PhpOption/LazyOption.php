<?php

















namespace PhpOption;

use Traversable;

/**
@template
@extends

*/
final class LazyOption extends Option
{

private $callback;


private $arguments;


private $option;

/**
@template




*/
public static function create($callback, array $arguments = []): self
{
return new self($callback, $arguments);
}





public function __construct($callback, array $arguments = [])
{
if (!is_callable($callback)) {
throw new \InvalidArgumentException('Invalid callback given');
}

$this->callback = $callback;
$this->arguments = $arguments;
}

public function isDefined(): bool
{
return $this->option()->isDefined();
}

public function isEmpty(): bool
{
return $this->option()->isEmpty();
}

public function get()
{
return $this->option()->get();
}

public function getOrElse($default)
{
return $this->option()->getOrElse($default);
}

public function getOrCall($callable)
{
return $this->option()->getOrCall($callable);
}

public function getOrThrow(\Exception $ex)
{
return $this->option()->getOrThrow($ex);
}

public function orElse(Option $else)
{
return $this->option()->orElse($else);
}

public function ifDefined($callable)
{
$this->option()->forAll($callable);
}

public function forAll($callable)
{
return $this->option()->forAll($callable);
}

public function map($callable)
{
return $this->option()->map($callable);
}

public function flatMap($callable)
{
return $this->option()->flatMap($callable);
}

public function filter($callable)
{
return $this->option()->filter($callable);
}

public function filterNot($callable)
{
return $this->option()->filterNot($callable);
}

public function select($value)
{
return $this->option()->select($value);
}

public function reject($value)
{
return $this->option()->reject($value);
}




public function getIterator(): Traversable
{
return $this->option()->getIterator();
}

public function foldLeft($initialValue, $callable)
{
return $this->option()->foldLeft($initialValue, $callable);
}

public function foldRight($initialValue, $callable)
{
return $this->option()->foldRight($initialValue, $callable);
}




private function option(): Option
{
if (null === $this->option) {

$option = call_user_func_array($this->callback, $this->arguments);
if ($option instanceof Option) {
$this->option = $option;
} else {
throw new \RuntimeException(sprintf('Expected instance of %s', Option::class));
}
}

return $this->option;
}
}
