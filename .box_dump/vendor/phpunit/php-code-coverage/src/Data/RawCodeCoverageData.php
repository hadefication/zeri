<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Data;

use function array_diff;
use function array_diff_key;
use function array_flip;
use function array_intersect;
use function array_intersect_key;
use function count;
use function explode;
use function file_get_contents;
use function in_array;
use function is_file;
use function preg_replace;
use function range;
use function str_ends_with;
use function str_starts_with;
use function trim;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\StaticAnalysis\FileAnalyser;

/**
@phpstan-import-type
@phpstan-import-type
@phpstan-import-type


*/
final class RawCodeCoverageData
{



private static array $emptyLineCache = [];




private array $lineCoverage;




private array $functionCoverage;




public static function fromXdebugWithoutPathCoverage(array $rawCoverage): self
{
return new self($rawCoverage, []);
}




public static function fromXdebugWithPathCoverage(array $rawCoverage): self
{
$lineCoverage = [];
$functionCoverage = [];

foreach ($rawCoverage as $file => $fileCoverageData) {

foreach ($fileCoverageData['functions'] as $existingKey => $data) {
if (str_ends_with($existingKey, '}') && !str_starts_with($existingKey, '{')) { 
$newKey = preg_replace('/\{.*}$/', '', $existingKey);
$fileCoverageData['functions'][$newKey] = $data;
unset($fileCoverageData['functions'][$existingKey]);
}
}

$lineCoverage[$file] = $fileCoverageData['lines'];
$functionCoverage[$file] = $fileCoverageData['functions'];
}

return new self($lineCoverage, $functionCoverage);
}

public static function fromUncoveredFile(string $filename, FileAnalyser $analyser): self
{
$lineCoverage = [];

foreach ($analyser->executableLinesIn($filename) as $line => $branch) {
$lineCoverage[$line] = Driver::LINE_NOT_EXECUTED;
}

return new self([$filename => $lineCoverage], []);
}





private function __construct(array $lineCoverage, array $functionCoverage)
{
$this->lineCoverage = $lineCoverage;
$this->functionCoverage = $functionCoverage;

$this->skipEmptyLines();
}

public function clear(): void
{
$this->lineCoverage = $this->functionCoverage = [];
}




public function lineCoverage(): array
{
return $this->lineCoverage;
}




public function functionCoverage(): array
{
return $this->functionCoverage;
}

public function removeCoverageDataForFile(string $filename): void
{
unset($this->lineCoverage[$filename], $this->functionCoverage[$filename]);
}




public function keepLineCoverageDataOnlyForLines(string $filename, array $lines): void
{
if (!isset($this->lineCoverage[$filename])) {
return;
}

$this->lineCoverage[$filename] = array_intersect_key(
$this->lineCoverage[$filename],
array_flip($lines),
);
}




public function markExecutableLineByBranch(string $filename, array $linesToBranchMap): void
{
if (!isset($this->lineCoverage[$filename])) {
return;
}

$linesByBranch = [];

foreach ($linesToBranchMap as $line => $branch) {
$linesByBranch[$branch][] = $line;
}

foreach ($this->lineCoverage[$filename] as $line => $lineStatus) {
if (!isset($linesToBranchMap[$line])) {
continue;
}

$branch = $linesToBranchMap[$line];

if (!isset($linesByBranch[$branch])) {
continue;
}

foreach ($linesByBranch[$branch] as $lineInBranch) {
$this->lineCoverage[$filename][$lineInBranch] = $lineStatus;
}

if (Driver::LINE_EXECUTED === $lineStatus) {
unset($linesByBranch[$branch]);
}
}
}




public function keepFunctionCoverageDataOnlyForLines(string $filename, array $lines): void
{
if (!isset($this->functionCoverage[$filename])) {
return;
}

foreach ($this->functionCoverage[$filename] as $functionName => $functionData) {
foreach ($functionData['branches'] as $branchId => $branch) {
if (count(array_diff(range($branch['line_start'], $branch['line_end']), $lines)) > 0) {
unset($this->functionCoverage[$filename][$functionName]['branches'][$branchId]);

foreach ($functionData['paths'] as $pathId => $path) {
if (in_array($branchId, $path['path'], true)) {
unset($this->functionCoverage[$filename][$functionName]['paths'][$pathId]);
}
}
}
}
}
}




public function removeCoverageDataForLines(string $filename, array $lines): void
{
if (empty($lines)) {
return;
}

if (!isset($this->lineCoverage[$filename])) {
return;
}

$this->lineCoverage[$filename] = array_diff_key(
$this->lineCoverage[$filename],
array_flip($lines),
);

if (isset($this->functionCoverage[$filename])) {
foreach ($this->functionCoverage[$filename] as $functionName => $functionData) {
foreach ($functionData['branches'] as $branchId => $branch) {
if (count(array_intersect($lines, range($branch['line_start'], $branch['line_end']))) > 0) {
unset($this->functionCoverage[$filename][$functionName]['branches'][$branchId]);

foreach ($functionData['paths'] as $pathId => $path) {
if (in_array($branchId, $path['path'], true)) {
unset($this->functionCoverage[$filename][$functionName]['paths'][$pathId]);
}
}
}
}
}
}
}









private function skipEmptyLines(): void
{
foreach ($this->lineCoverage as $filename => $coverage) {
foreach ($this->getEmptyLinesForFile($filename) as $emptyLine) {
unset($this->lineCoverage[$filename][$emptyLine]);
}
}
}

private function getEmptyLinesForFile(string $filename): array
{
if (!isset(self::$emptyLineCache[$filename])) {
self::$emptyLineCache[$filename] = [];

if (is_file($filename)) {
$sourceLines = explode("\n", file_get_contents($filename));

foreach ($sourceLines as $line => $source) {
if (trim($source) === '') {
self::$emptyLineCache[$filename][] = ($line + 1);
}
}
}
}

return self::$emptyLineCache[$filename];
}
}
