<?php

namespace Illuminate\Foundation\Concerns;

use Throwable;

trait ResolvesDumpSource
{





protected $editorHrefs = [
'atom' => 'atom://core/open/file?filename={file}&line={line}',
'cursor' => 'cursor://file/{file}:{line}',
'emacs' => 'emacs://open?url=file://{file}&line={line}',
'idea' => 'idea://open?file={file}&line={line}',
'macvim' => 'mvim://open/?url=file://{file}&line={line}',
'netbeans' => 'netbeans://open/?f={file}:{line}',
'nova' => 'nova://core/open/file?filename={file}&line={line}',
'phpstorm' => 'phpstorm://open?file={file}&line={line}',
'sublime' => 'subl://open?url=file://{file}&line={line}',
'textmate' => 'txmt://open?url=file://{file}&line={line}',
'vscode' => 'vscode://file/{file}:{line}',
'vscode-insiders' => 'vscode-insiders://file/{file}:{line}',
'vscode-insiders-remote' => 'vscode-insiders://vscode-remote/{file}:{line}',
'vscode-remote' => 'vscode://vscode-remote/{file}:{line}',
'vscodium' => 'vscodium://file/{file}:{line}',
'xdebug' => 'xdebug://{file}@{line}',
];






protected static $adjustableTraces = [
'symfony/var-dumper/Resources/functions/dump.php' => 1,
'Illuminate/Collections/Traits/EnumeratesValues.php' => 4,
];






protected static $dumpSourceResolver;






public function resolveDumpSource()
{
if (static::$dumpSourceResolver === false) {
return null;
}

if (static::$dumpSourceResolver) {
return call_user_func(static::$dumpSourceResolver);
}

$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);

$sourceKey = null;

foreach ($trace as $traceKey => $traceFile) {
if (! isset($traceFile['file'])) {
continue;
}

foreach (self::$adjustableTraces as $name => $key) {
if (str_ends_with(
$traceFile['file'],
str_replace('/', DIRECTORY_SEPARATOR, $name)
)) {
$sourceKey = $traceKey + $key;
break;
}
}

if (! is_null($sourceKey)) {
break;
}
}

if (is_null($sourceKey)) {
return;
}

$file = $trace[$sourceKey]['file'] ?? null;
$line = $trace[$sourceKey]['line'] ?? null;

if (is_null($file) || is_null($line)) {
return;
}

$relativeFile = $file;

if ($this->isCompiledViewFile($file)) {
$file = $this->getOriginalFileForCompiledView($file);
$line = null;
}

if (str_starts_with($file, $this->basePath)) {
$relativeFile = substr($file, strlen($this->basePath) + 1);
}

return [$file, $relativeFile, $line];
}







protected function isCompiledViewFile($file)
{
return str_starts_with($file, $this->compiledViewPath) && str_ends_with($file, '.php');
}







protected function getOriginalFileForCompiledView($file)
{
preg_match('/\/\*\*PATH\s(.*)\sENDPATH/', file_get_contents($file), $matches);

if (isset($matches[1])) {
$file = $matches[1];
}

return $file;
}








protected function resolveSourceHref($file, $line)
{
try {
$editor = config('app.editor');
} catch (Throwable) {

}

if (! isset($editor)) {
return;
}

$href = is_array($editor) && isset($editor['href'])
? $editor['href']
: ($this->editorHrefs[$editor['name'] ?? $editor] ?? sprintf('%s://open?file={file}&line={line}', $editor['name'] ?? $editor));

if ($basePath = $editor['base_path'] ?? false) {
$file = str_replace($this->basePath, $basePath, $file);
}

return str_replace(
['{file}', '{line}'],
[$file, is_null($line) ? 1 : $line],
$href,
);
}







public static function resolveDumpSourceUsing($callable)
{
static::$dumpSourceResolver = $callable;
}






public static function dontIncludeSource()
{
static::$dumpSourceResolver = false;
}
}
