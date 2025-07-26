<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Report\Html;

/**
@immutable
*/
final class Colors
{
private readonly string $successLow;
private readonly string $successMedium;
private readonly string $successHigh;
private readonly string $warning;
private readonly string $danger;

public static function default(): self
{
return new self('#dff0d8', '#c3e3b5', '#99cb84', '#fcf8e3', '#f2dede');
}

public static function from(string $successLow, string $successMedium, string $successHigh, string $warning, string $danger): self
{
return new self($successLow, $successMedium, $successHigh, $warning, $danger);
}

private function __construct(string $successLow, string $successMedium, string $successHigh, string $warning, string $danger)
{
$this->successLow = $successLow;
$this->successMedium = $successMedium;
$this->successHigh = $successHigh;
$this->warning = $warning;
$this->danger = $danger;
}

public function successLow(): string
{
return $this->successLow;
}

public function successMedium(): string
{
return $this->successMedium;
}

public function successHigh(): string
{
return $this->successHigh;
}

public function warning(): string
{
return $this->warning;
}

public function danger(): string
{
return $this->danger;
}
}
