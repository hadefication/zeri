<?php declare(strict_types=1);








namespace SebastianBergmann\FileIterator;

use function assert;
use function str_starts_with;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use SplFileInfo;




final class ExcludeIterator extends RecursiveFilterIterator
{



private array $exclude;




public function __construct(RecursiveDirectoryIterator $iterator, array $exclude)
{
parent::__construct($iterator);

$this->exclude = $exclude;
}

public function accept(): bool
{
$current = $this->current();

assert($current instanceof SplFileInfo);

$path = $current->getRealPath();

if ($path === false) {
return false;
}

foreach ($this->exclude as $exclude) {
if (str_starts_with($path, $exclude)) {
return false;
}
}

return true;
}

public function hasChildren(): bool
{
return $this->getInnerIterator()->hasChildren();
}

public function getChildren(): self
{
return new self(
$this->getInnerIterator()->getChildren(),
$this->exclude,
);
}

public function getInnerIterator(): RecursiveDirectoryIterator
{
$innerIterator = parent::getInnerIterator();

assert($innerIterator instanceof RecursiveDirectoryIterator);

return $innerIterator;
}
}
