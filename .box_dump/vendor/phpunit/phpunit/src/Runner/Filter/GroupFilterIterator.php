<?php declare(strict_types=1);








namespace PHPUnit\Runner\Filter;

use function array_merge;
use function array_push;
use function in_array;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Runner\PhptTestCase;
use RecursiveFilterIterator;
use RecursiveIterator;

/**
@no-named-arguments


*/
abstract class GroupFilterIterator extends RecursiveFilterIterator
{



private readonly array $groupTests;





public function __construct(RecursiveIterator $iterator, array $groups, TestSuite $suite)
{
parent::__construct($iterator);

$groupTests = [];

foreach ($suite->groups() as $group => $tests) {
if (in_array($group, $groups, true)) {
$groupTests = array_merge($groupTests, $tests);

array_push($groupTests, ...$groupTests);
}
}

$this->groupTests = $groupTests;
}

public function accept(): bool
{
$test = $this->getInnerIterator()->current();

if ($test instanceof TestSuite) {
return true;
}

if ($test instanceof TestCase || $test instanceof PhptTestCase) {
return $this->doAccept($test->valueObjectForEvents()->id(), $this->groupTests);
}

return true;
}





abstract protected function doAccept(string $id, array $groupTests): bool;
}
