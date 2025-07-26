<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\StaticAnalysis;

use SebastianBergmann\CodeCoverage\Filter;

final class CacheWarmer
{
public function warmCache(string $cacheDirectory, bool $useAnnotationsForIgnoringCode, bool $ignoreDeprecatedCode, Filter $filter): void
{
$analyser = new CachingFileAnalyser(
$cacheDirectory,
new ParsingFileAnalyser(
$useAnnotationsForIgnoringCode,
$ignoreDeprecatedCode,
),
$useAnnotationsForIgnoringCode,
$ignoreDeprecatedCode,
);

foreach ($filter->files() as $file) {
$analyser->process($file);
}
}
}
