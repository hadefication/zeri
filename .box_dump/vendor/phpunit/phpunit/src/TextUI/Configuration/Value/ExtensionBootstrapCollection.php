<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use IteratorAggregate;

/**
@template-implements
@no-named-arguments
@immutable


*/
final readonly class ExtensionBootstrapCollection implements IteratorAggregate
{



private array $extensionBootstraps;




public static function fromArray(array $extensionBootstraps): self
{
return new self(...$extensionBootstraps);
}

private function __construct(ExtensionBootstrap ...$extensionBootstraps)
{
$this->extensionBootstraps = $extensionBootstraps;
}




public function asArray(): array
{
return $this->extensionBootstraps;
}

public function getIterator(): ExtensionBootstrapCollectionIterator
{
return new ExtensionBootstrapCollectionIterator($this);
}
}
