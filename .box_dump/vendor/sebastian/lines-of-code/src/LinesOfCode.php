<?php declare(strict_types=1);








namespace SebastianBergmann\LinesOfCode;

/**
@immutable
*/
final readonly class LinesOfCode
{



private int $linesOfCode;




private int $commentLinesOfCode;




private int $nonCommentLinesOfCode;




private int $logicalLinesOfCode;










public function __construct(int $linesOfCode, int $commentLinesOfCode, int $nonCommentLinesOfCode, int $logicalLinesOfCode)
{
/**
@phpstan-ignore */
if ($linesOfCode < 0) {
throw new NegativeValueException('$linesOfCode must not be negative');
}

/**
@phpstan-ignore */
if ($commentLinesOfCode < 0) {
throw new NegativeValueException('$commentLinesOfCode must not be negative');
}

/**
@phpstan-ignore */
if ($nonCommentLinesOfCode < 0) {
throw new NegativeValueException('$nonCommentLinesOfCode must not be negative');
}

/**
@phpstan-ignore */
if ($logicalLinesOfCode < 0) {
throw new NegativeValueException('$logicalLinesOfCode must not be negative');
}

if ($linesOfCode - $commentLinesOfCode !== $nonCommentLinesOfCode) {
throw new IllogicalValuesException('$linesOfCode !== $commentLinesOfCode + $nonCommentLinesOfCode');
}

$this->linesOfCode = $linesOfCode;
$this->commentLinesOfCode = $commentLinesOfCode;
$this->nonCommentLinesOfCode = $nonCommentLinesOfCode;
$this->logicalLinesOfCode = $logicalLinesOfCode;
}




public function linesOfCode(): int
{
return $this->linesOfCode;
}




public function commentLinesOfCode(): int
{
return $this->commentLinesOfCode;
}




public function nonCommentLinesOfCode(): int
{
return $this->nonCommentLinesOfCode;
}




public function logicalLinesOfCode(): int
{
return $this->logicalLinesOfCode;
}

public function plus(self $other): self
{
return new self(
$this->linesOfCode() + $other->linesOfCode(),
$this->commentLinesOfCode() + $other->commentLinesOfCode(),
$this->nonCommentLinesOfCode() + $other->nonCommentLinesOfCode(),
$this->logicalLinesOfCode() + $other->logicalLinesOfCode(),
);
}
}
