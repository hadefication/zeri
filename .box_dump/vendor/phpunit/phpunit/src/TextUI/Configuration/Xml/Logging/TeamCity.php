<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration\Logging;

use PHPUnit\TextUI\Configuration\File;

/**
@no-named-arguments
@immutable



*/
final readonly class TeamCity
{
private File $target;

public function __construct(File $target)
{
$this->target = $target;
}

public function target(): File
{
return $this->target;
}
}
