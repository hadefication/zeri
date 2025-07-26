<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

/**
@no-named-arguments
@immutable

*/
final readonly class FilterDirectory
{



private string $path;
private string $prefix;
private string $suffix;




public function __construct(string $path, string $prefix, string $suffix)
{
$this->path = $path;
$this->prefix = $prefix;
$this->suffix = $suffix;
}




public function path(): string
{
return $this->path;
}

public function prefix(): string
{
return $this->prefix;
}

public function suffix(): string
{
return $this->suffix;
}
}
