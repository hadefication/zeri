<?php declare(strict_types=1);








namespace PHPUnit\Runner\Extension;

use function array_key_exists;
use PHPUnit\Runner\ParameterDoesNotExistException;

/**
@immutable
@no-named-arguments

*/
final readonly class ParameterCollection
{



private array $parameters;




public static function fromArray(array $parameters): self
{
return new self($parameters);
}




private function __construct(array $parameters)
{
$this->parameters = $parameters;
}

public function has(string $name): bool
{
return array_key_exists($name, $this->parameters);
}




public function get(string $name): string
{
if (!$this->has($name)) {
throw new ParameterDoesNotExistException($name);
}

return $this->parameters[$name];
}
}
