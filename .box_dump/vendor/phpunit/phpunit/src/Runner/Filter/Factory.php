<?php declare(strict_types=1);








namespace PHPUnit\Runner\Filter;

use function assert;
use FilterIterator;
use Iterator;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestSuite;

/**
@no-named-arguments


*/
final class Factory
{



private array $filters = [];




public function addTestIdFilter(array $testIds): void
{
$this->filters[] = [
'className' => TestIdFilterIterator::class,
'argument' => $testIds,
];
}




public function addIncludeGroupFilter(array $groups): void
{
$this->filters[] = [
'className' => IncludeGroupFilterIterator::class,
'argument' => $groups,
];
}




public function addExcludeGroupFilter(array $groups): void
{
$this->filters[] = [
'className' => ExcludeGroupFilterIterator::class,
'argument' => $groups,
];
}




public function addIncludeNameFilter(string $name): void
{
$this->filters[] = [
'className' => IncludeNameFilterIterator::class,
'argument' => $name,
];
}




public function addExcludeNameFilter(string $name): void
{
$this->filters[] = [
'className' => ExcludeNameFilterIterator::class,
'argument' => $name,
];
}






public function factory(Iterator $iterator, TestSuite $suite): FilterIterator
{
foreach ($this->filters as $filter) {
$iterator = new $filter['className'](
$iterator,
$filter['argument'],
$suite,
);
}

assert($iterator instanceof FilterIterator);

return $iterator;
}
}
