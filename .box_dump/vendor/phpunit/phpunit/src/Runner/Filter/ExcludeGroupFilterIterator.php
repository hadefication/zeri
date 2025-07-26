<?php declare(strict_types=1);








namespace PHPUnit\Runner\Filter;

use function in_array;

/**
@no-named-arguments


*/
final class ExcludeGroupFilterIterator extends GroupFilterIterator
{




protected function doAccept(string $id, array $groupTests): bool
{
return !in_array($id, $groupTests, true);
}
}
