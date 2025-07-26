<?php










namespace Symfony\Component\Finder\Iterator;

/**
@extends




*/
class FileTypeFilterIterator extends \FilterIterator
{
public const ONLY_FILES = 1;
public const ONLY_DIRECTORIES = 2;





public function __construct(
\Iterator $iterator,
private int $mode,
) {
parent::__construct($iterator);
}




public function accept(): bool
{
$fileinfo = $this->current();
if (self::ONLY_DIRECTORIES === (self::ONLY_DIRECTORIES & $this->mode) && $fileinfo->isFile()) {
return false;
} elseif (self::ONLY_FILES === (self::ONLY_FILES & $this->mode) && $fileinfo->isDir()) {
return false;
}

return true;
}
}
