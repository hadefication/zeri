<?php declare(strict_types=1);








namespace PHPUnit\Runner\Filter;

/**
@no-named-arguments


*/
final class ExcludeNameFilterIterator extends NameFilterIterator
{
protected function doAccept(bool $result): bool
{
return !$result;
}
}
