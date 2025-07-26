<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report;

use PHPUnit\TextUI\Configuration\File;

/**
@no-named-arguments
@immutable



*/
final readonly class Crap4j
{
private File $target;
private int $threshold;

public function __construct(File $target, int $threshold)
{
$this->target = $target;
$this->threshold = $threshold;
}

public function target(): File
{
return $this->target;
}

public function threshold(): int
{
return $this->threshold;
}
}
