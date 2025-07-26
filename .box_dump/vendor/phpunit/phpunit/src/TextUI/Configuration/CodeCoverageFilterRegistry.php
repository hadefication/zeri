<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

use function array_keys;
use function assert;
use SebastianBergmann\CodeCoverage\Filter;

/**
@no-named-arguments





*/
final class CodeCoverageFilterRegistry
{
private static ?self $instance = null;
private ?Filter $filter = null;
private bool $configured = false;

public static function instance(): self
{
if (self::$instance === null) {
self::$instance = new self;
}

return self::$instance;
}




public function get(): Filter
{
assert($this->filter !== null);

return $this->filter;
}




public function init(Configuration $configuration, bool $force = false): void
{
if (!$configuration->hasCoverageReport() && !$force) {
return;
}

if ($this->configured && !$force) {
return;
}

$this->filter = new Filter;

if ($configuration->source()->notEmpty()) {
$this->filter->includeFiles(array_keys((new SourceMapper)->map($configuration->source())));

$this->configured = true;
}
}




public function configured(): bool
{
return $this->configured;
}
}
