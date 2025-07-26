<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage;

use RuntimeException;

final class UnintentionallyCoveredCodeException extends RuntimeException implements Exception
{



private readonly array $unintentionallyCoveredUnits;




public function __construct(array $unintentionallyCoveredUnits)
{
$this->unintentionallyCoveredUnits = $unintentionallyCoveredUnits;

parent::__construct($this->toString());
}




public function getUnintentionallyCoveredUnits(): array
{
return $this->unintentionallyCoveredUnits;
}

private function toString(): string
{
$message = '';

foreach ($this->unintentionallyCoveredUnits as $unit) {
$message .= '- ' . $unit . "\n";
}

return $message;
}
}
