<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

/**
@no-named-arguments
@immutable

*/
final readonly class ExtensionBootstrap
{



private string $className;




private array $parameters;





public function __construct(string $className, array $parameters)
{
$this->className = $className;
$this->parameters = $parameters;
}




public function className(): string
{
return $this->className;
}




public function parameters(): array
{
return $this->parameters;
}
}
