<?php

namespace Illuminate\Foundation\Exceptions\Renderer\Mappers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\ViewException;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Throwable;































class BladeMapper
{





protected $factory;






protected $bladeCompiler;







public function __construct(Factory $factory, BladeCompiler $bladeCompiler)
{
$this->factory = $factory;
$this->bladeCompiler = $bladeCompiler;
}







public function map(FlattenException $exception)
{
while ($exception->getClass() === ViewException::class) {
if (($previous = $exception->getPrevious()) === null) {
break;
}

$exception = $previous;
}

$trace = (new Collection($exception->getTrace()))
->map(function ($frame) {
if ($originalPath = $this->findCompiledView((string) Arr::get($frame, 'file', ''))) {
$frame['file'] = $originalPath;
$frame['line'] = $this->detectLineNumber($frame['file'], $frame['line']);
}

return $frame;
})->toArray();

return tap($exception, fn () => (fn () => $this->trace = $trace)->call($exception));
}







protected function findCompiledView(string $compiledPath)
{
return once(fn () => $this->getKnownPaths())[$compiledPath] ?? null;
}






protected function getKnownPaths()
{
$compilerEngineReflection = new ReflectionClass(
$bladeCompilerEngine = $this->factory->getEngineResolver()->resolve('blade'),
);

if (! $compilerEngineReflection->hasProperty('lastCompiled') && $compilerEngineReflection->hasProperty('engine')) {
$compilerEngine = $compilerEngineReflection->getProperty('engine');
$compilerEngine = $compilerEngine->getValue($bladeCompilerEngine);
$lastCompiled = new ReflectionProperty($compilerEngine, 'lastCompiled');
$lastCompiled = $lastCompiled->getValue($compilerEngine);
} else {
$lastCompiled = $compilerEngineReflection->getProperty('lastCompiled');
$lastCompiled = $lastCompiled->getValue($bladeCompilerEngine);
}

$knownPaths = [];
foreach ($lastCompiled as $lastCompiledPath) {
$compiledPath = $bladeCompilerEngine->getCompiler()->getCompiledPath($lastCompiledPath);

$knownPaths[realpath($compiledPath ?? $lastCompiledPath)] = realpath($lastCompiledPath);
}

return $knownPaths;
}







protected function filterViewData(array $data)
{
return array_filter($data, function ($value, $key) {
if ($key === 'app') {
return ! $value instanceof Application;
}

return $key !== '__env';
}, ARRAY_FILTER_USE_BOTH);
}








protected function detectLineNumber(string $filename, int $compiledLineNumber)
{
$map = $this->compileSourcemap((string) file_get_contents($filename));

return $this->findClosestLineNumberMapping($map, $compiledLineNumber);
}







protected function compileSourcemap(string $value)
{
try {
$value = $this->addEchoLineNumbers($value);
$value = $this->addStatementLineNumbers($value);
$value = $this->addBladeComponentLineNumbers($value);

$value = $this->bladeCompiler->compileString($value);

return $this->trimEmptyLines($value);
} catch (Throwable $e) {
report($e);

return $value;
}
}







protected function addEchoLineNumbers(string $value)
{
$echoPairs = [['{{', '}}'], ['{{{', '}}}'], ['{!!', '!!}']];

foreach ($echoPairs as $pair) {

$pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $pair[0], $pair[1]);

if (preg_match_all($pattern, $value, $matches, PREG_OFFSET_CAPTURE)) {
foreach (array_reverse($matches[0]) as $match) {
$position = mb_strlen(substr($value, 0, $match[1]));

$value = $this->insertLineNumberAtPosition($position, $value);
}
}
}

return $value;
}







protected function addStatementLineNumbers(string $value)
{
$shouldInsertLineNumbers = preg_match_all(
'/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x',
$value,
$matches,
PREG_OFFSET_CAPTURE
);

if ($shouldInsertLineNumbers) {
foreach (array_reverse($matches[0]) as $match) {
$position = mb_strlen(substr($value, 0, $match[1]));

$value = $this->insertLineNumberAtPosition($position, $value);
}
}

return $value;
}







protected function addBladeComponentLineNumbers(string $value)
{
$shouldInsertLineNumbers = preg_match_all(
'/<\s*x[-:]([\w\-:.]*)/mx',
$value,
$matches,
PREG_OFFSET_CAPTURE
);

if ($shouldInsertLineNumbers) {
foreach (array_reverse($matches[0]) as $match) {
$position = mb_strlen(substr($value, 0, $match[1]));

$value = $this->insertLineNumberAtPosition($position, $value);
}
}

return $value;
}








protected function insertLineNumberAtPosition(int $position, string $value)
{
$before = mb_substr($value, 0, $position);

$lineNumber = count(explode("\n", $before));

return mb_substr($value, 0, $position)."|---LINE:{$lineNumber}---|".mb_substr($value, $position);
}







protected function trimEmptyLines(string $value)
{
$value = preg_replace('/^\|---LINE:([0-9]+)---\|$/m', '', $value);

return ltrim((string) $value, PHP_EOL);
}








protected function findClosestLineNumberMapping(string $map, int $compiledLineNumber)
{
$map = explode("\n", $map);

$maxDistance = 20;

$pattern = '/\|---LINE:(?P<line>[0-9]+)---\|/m';

$lineNumberToCheck = $compiledLineNumber - 1;

while (true) {
if ($lineNumberToCheck < $compiledLineNumber - $maxDistance) {
return min($compiledLineNumber, count($map));
}

if (preg_match($pattern, $map[$lineNumberToCheck] ?? '', $matches)) {
return (int) $matches['line'];
}

$lineNumberToCheck--;
}
}
}
