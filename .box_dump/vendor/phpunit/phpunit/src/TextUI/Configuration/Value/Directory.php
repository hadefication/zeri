<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

/**
@no-named-arguments
@immutable

*/
final readonly class Directory
{
private string $path;

public function __construct(string $path)
{
$this->path = $path;
}

public function path(): string
{
return $this->path;
}
}
