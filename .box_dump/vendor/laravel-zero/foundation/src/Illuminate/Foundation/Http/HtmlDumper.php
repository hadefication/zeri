<?php

namespace Illuminate\Foundation\Http;

use Illuminate\Foundation\Concerns\ResolvesDumpSource;
use Symfony\Component\VarDumper\Caster\ReflectionCaster;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper as BaseHtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

class HtmlDumper extends BaseHtmlDumper
{
use ResolvesDumpSource;






const EXPANDED_SEPARATOR = 'class=sf-dump-expanded>';






const NON_EXPANDED_SEPARATOR = "\n</pre><script>";






protected $basePath;






protected $compiledViewPath;






protected $dumping = false;







public function __construct($basePath, $compiledViewPath)
{
parent::__construct();

$this->basePath = $basePath;
$this->compiledViewPath = $compiledViewPath;
}








public static function register($basePath, $compiledViewPath)
{
$cloner = tap(new VarCloner())->addCasters(ReflectionCaster::UNSET_CLOSURE_FILE_INFO);

$dumper = new static($basePath, $compiledViewPath);

VarDumper::setHandler(fn ($value) => $dumper->dumpWithSource($cloner->cloneVar($value)));
}







public function dumpWithSource(Data $data)
{
if ($this->dumping) {
$this->dump($data);

return;
}

$this->dumping = true;

$output = (string) $this->dump($data, true);

$output = match (true) {
str_contains($output, static::EXPANDED_SEPARATOR) => str_replace(
static::EXPANDED_SEPARATOR,
static::EXPANDED_SEPARATOR.$this->getDumpSourceContent(),
$output,
),
str_contains($output, static::NON_EXPANDED_SEPARATOR) => str_replace(
static::NON_EXPANDED_SEPARATOR,
$this->getDumpSourceContent().static::NON_EXPANDED_SEPARATOR,
$output,
),
default => $output,
};

fwrite($this->outputStream, $output);

$this->dumping = false;
}






protected function getDumpSourceContent()
{
if (is_null($dumpSource = $this->resolveDumpSource())) {
return '';
}

[$file, $relativeFile, $line] = $dumpSource;

$source = sprintf('%s%s', $relativeFile, is_null($line) ? '' : ":$line");

if ($href = $this->resolveSourceHref($file, $line)) {
$source = sprintf('<a href="%s">%s</a>', $href, $source);
}

return sprintf('<span style="color: #A0A0A0;"> // %s</span>', $source);
}
}
