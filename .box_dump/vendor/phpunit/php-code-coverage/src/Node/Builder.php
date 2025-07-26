<?php declare(strict_types=1);








namespace SebastianBergmann\CodeCoverage\Node;

use const DIRECTORY_SEPARATOR;
use function array_shift;
use function basename;
use function count;
use function dirname;
use function explode;
use function implode;
use function is_file;
use function str_ends_with;
use function str_replace;
use function str_starts_with;
use function substr;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;
use SebastianBergmann\CodeCoverage\StaticAnalysis\FileAnalyser;

/**
@phpstan-import-type


*/
final class Builder
{
private readonly FileAnalyser $analyser;

public function __construct(FileAnalyser $analyser)
{
$this->analyser = $analyser;
}

public function build(CodeCoverage $coverage): Directory
{
$data = clone $coverage->getData(); 
$commonPath = $this->reducePaths($data);
$root = new Directory(
$commonPath,
null,
);

$this->addItems(
$root,
$this->buildDirectoryStructure($data),
$coverage->getTests(),
);

return $root;
}




private function addItems(Directory $root, array $items, array $tests): void
{
foreach ($items as $key => $value) {
$key = (string) $key;

if (str_ends_with($key, '/f')) {
$key = substr($key, 0, -2);
$filename = $root->pathAsString() . DIRECTORY_SEPARATOR . $key;

if (is_file($filename)) {
$root->addFile(
new File(
$key,
$root,
$value['lineCoverage'],
$value['functionCoverage'],
$tests,
$this->analyser->classesIn($filename),
$this->analyser->traitsIn($filename),
$this->analyser->functionsIn($filename),
$this->analyser->linesOfCodeFor($filename),
),
);
}
} else {
$child = $root->addDirectory($key);

$this->addItems($child, $value, $tests);
}
}
}











































private function buildDirectoryStructure(ProcessedCodeCoverageData $data): array
{
$result = [];

foreach ($data->coveredFiles() as $originalPath) {
$path = explode(DIRECTORY_SEPARATOR, $originalPath);
$pointer = &$result;
$max = count($path);

for ($i = 0; $i < $max; $i++) {
$type = '';

if ($i === ($max - 1)) {
$type = '/f';
}

$pointer = &$pointer[$path[$i] . $type];
}

$pointer = [
'lineCoverage' => $data->lineCoverage()[$originalPath] ?? [],
'functionCoverage' => $data->functionCoverage()[$originalPath] ?? [],
];
}

return $result;
}






































private function reducePaths(ProcessedCodeCoverageData $coverage): string
{
if (empty($coverage->coveredFiles())) {
return '.';
}

$commonPath = '';
$paths = $coverage->coveredFiles();

if (count($paths) === 1) {
$commonPath = dirname($paths[0]) . DIRECTORY_SEPARATOR;
$coverage->renameFile($paths[0], basename($paths[0]));

return $commonPath;
}

$max = count($paths);

for ($i = 0; $i < $max; $i++) {

if (str_starts_with($paths[$i], 'phar://')) {
$paths[$i] = substr($paths[$i], 7);
$paths[$i] = str_replace('/', DIRECTORY_SEPARATOR, $paths[$i]);
}
$paths[$i] = explode(DIRECTORY_SEPARATOR, $paths[$i]);

if (empty($paths[$i][0])) {
$paths[$i][0] = DIRECTORY_SEPARATOR;
}
}

$done = false;
$max = count($paths);

while (!$done) {
for ($i = 0; $i < $max - 1; $i++) {
if (!isset($paths[$i][0]) ||
!isset($paths[$i + 1][0]) ||
$paths[$i][0] !== $paths[$i + 1][0]) {
$done = true;

break;
}
}

if (!$done) {
$commonPath .= $paths[0][0];

if ($paths[0][0] !== DIRECTORY_SEPARATOR) {
$commonPath .= DIRECTORY_SEPARATOR;
}

for ($i = 0; $i < $max; $i++) {
array_shift($paths[$i]);
}
}
}

$original = $coverage->coveredFiles();
$max = count($original);

for ($i = 0; $i < $max; $i++) {
$coverage->renameFile($original[$i], implode(DIRECTORY_SEPARATOR, $paths[$i]));
}

return substr($commonPath, 0, -1);
}
}
