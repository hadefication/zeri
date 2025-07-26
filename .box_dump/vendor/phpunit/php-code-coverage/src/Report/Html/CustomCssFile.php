<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Report\Html;

use function is_file;
use SebastianBergmann\CodeCoverage\InvalidArgumentException;

/**
@immutable
*/
final class CustomCssFile
{
private readonly string $path;

public static function default(): self
{
return new self(__DIR__ . '/Renderer/Template/css/custom.css');
}




public static function from(string $path): self
{
if (!is_file($path)) {
throw new InvalidArgumentException(
'$path does not exist',
);
}

return new self($path);
}

private function __construct(string $path)
{
$this->path = $path;
}

public function path(): string
{
return $this->path;
}
}
