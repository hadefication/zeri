<?php declare(strict_types=1);








namespace PHPUnit\Runner\Baseline;

/**
@no-named-arguments


*/
final class Baseline
{
public const VERSION = 1;




private array $issues = [];

public function add(Issue $issue): void
{
if (!isset($this->issues[$issue->file()])) {
$this->issues[$issue->file()] = [];
}

if (!isset($this->issues[$issue->file()][$issue->line()])) {
$this->issues[$issue->file()][$issue->line()] = [];
}

$this->issues[$issue->file()][$issue->line()][] = $issue;
}

public function has(Issue $issue): bool
{
if (!isset($this->issues[$issue->file()][$issue->line()])) {
return false;
}

foreach ($this->issues[$issue->file()][$issue->line()] as $_issue) {
if ($_issue->equals($issue)) {
return true;
}
}

return false;
}




public function groupedByFileAndLine(): array
{
return $this->issues;
}
}
