<?php

declare(strict_types=1);

namespace Pest\Repositories;

use Pest\Exceptions\ShouldNotHappen;
use Pest\TestSuite;




final class SnapshotRepository
{

private static array $expectationsCounter = [];




public function __construct(
readonly private string $testsPath,
readonly private string $snapshotsPath,
) {}




public function has(): bool
{
return file_exists($this->getSnapshotFilename());
}








public function get(): array
{
$contents = file_get_contents($snapshotFilename = $this->getSnapshotFilename());

if ($contents === false) {
throw ShouldNotHappen::fromMessage('Snapshot file could not be read.');
}

$snapshot = str_replace(dirname($this->testsPath).'/', '', $snapshotFilename);

return [$snapshot, $contents];
}




public function save(string $snapshot): string
{
$snapshotFilename = $this->getSnapshotFilename();

if (! file_exists(dirname($snapshotFilename))) {
mkdir(dirname($snapshotFilename), 0755, true);
}

file_put_contents($snapshotFilename, $snapshot);

return str_replace(dirname($this->testsPath).'/', '', $snapshotFilename);
}




public function flush(): void
{
$absoluteSnapshotsPath = $this->testsPath.'/'.$this->snapshotsPath;

$deleteDirectory = function (string $path) use (&$deleteDirectory): void {
if (file_exists($path)) {
$scannedDir = scandir($path);
assert(is_array($scannedDir));

$files = array_diff($scannedDir, ['.', '..']);

foreach ($files as $file) {
if (is_dir($path.'/'.$file)) {
$deleteDirectory($path.'/'.$file);
} else {
unlink($path.'/'.$file);
}
}

rmdir($path);
}
};

if (file_exists($absoluteSnapshotsPath)) {
$deleteDirectory($absoluteSnapshotsPath);
}
}




private function getSnapshotFilename(): string
{
$relativePath = str_replace($this->testsPath, '', TestSuite::getInstance()->getFilename());


$relativePath = substr($relativePath, 0, (int) strrpos($relativePath, '.'));

$description = TestSuite::getInstance()->getDescription();

if ($this->getCurrentSnapshotCounter() > 1) {
$description .= '__'.$this->getCurrentSnapshotCounter();
}

return sprintf('%s/%s.snap', $this->testsPath.'/'.$this->snapshotsPath.$relativePath, $description);
}

private function getCurrentSnapshotKey(): string
{
return TestSuite::getInstance()->getFilename().'###'.TestSuite::getInstance()->getDescription();
}

private function getCurrentSnapshotCounter(): int
{
return self::$expectationsCounter[$this->getCurrentSnapshotKey()] ?? 0;
}

public function startNewExpectation(): void
{
$key = $this->getCurrentSnapshotKey();

if (! isset(self::$expectationsCounter[$key])) {
self::$expectationsCounter[$key] = 0;
}

self::$expectationsCounter[$key]++;
}
}
