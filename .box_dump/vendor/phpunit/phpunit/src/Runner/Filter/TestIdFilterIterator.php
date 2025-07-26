<?php declare(strict_types=1);








namespace PHPUnit\Runner\Filter;

use function in_array;
use PHPUnit\Event\TestData\NoDataSetFromDataProviderException;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Runner\PhptTestCase;
use RecursiveFilterIterator;
use RecursiveIterator;

/**
@no-named-arguments


*/
final class TestIdFilterIterator extends RecursiveFilterIterator
{



private readonly array $testIds;





public function __construct(RecursiveIterator $iterator, array $testIds)
{
parent::__construct($iterator);

$this->testIds = $testIds;
}

public function accept(): bool
{
$test = $this->getInnerIterator()->current();

if ($test instanceof TestSuite) {
return true;
}

if (!$test instanceof TestCase && !$test instanceof PhptTestCase) {
return false;
}

try {
return in_array($test->valueObjectForEvents()->id(), $this->testIds, true);
} catch (NoDataSetFromDataProviderException) {
return false;
}
}
}
