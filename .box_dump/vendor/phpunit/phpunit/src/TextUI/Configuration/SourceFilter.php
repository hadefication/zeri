<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

/**
@no-named-arguments


*/
final class SourceFilter
{
private static ?self $instance = null;




private readonly array $map;

public static function instance(): self
{
if (self::$instance === null) {
self::$instance = new self(
(new SourceMapper)->map(
Registry::get()->source(),
),
);
}

return self::$instance;
}




public function __construct(array $map)
{
$this->map = $map;
}

public function includes(string $path): bool
{
return isset($this->map[$path]);
}
}
