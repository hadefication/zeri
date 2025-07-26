<?php declare(strict_types=1);








namespace PHPUnit\Framework;

use Exception;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
@no-named-arguments








*/
final class ExpectationFailedException extends AssertionFailedError
{
protected ?ComparisonFailure $comparisonFailure = null;

public function __construct(string $message, ?ComparisonFailure $comparisonFailure = null, ?Exception $previous = null)
{
$this->comparisonFailure = $comparisonFailure;

parent::__construct($message, 0, $previous);
}

public function getComparisonFailure(): ?ComparisonFailure
{
return $this->comparisonFailure;
}
}
