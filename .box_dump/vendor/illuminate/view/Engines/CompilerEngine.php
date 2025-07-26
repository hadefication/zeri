<?php

namespace Illuminate\View\Engines;

use Illuminate\Database\RecordNotFoundException;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\CompilerInterface;
use Illuminate\View\ViewException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class CompilerEngine extends PhpEngine
{





protected $compiler;






protected $lastCompiled = [];






protected $compiledOrNotExpired = [];







public function __construct(CompilerInterface $compiler, ?Filesystem $files = null)
{
parent::__construct($files ?: new Filesystem);

$this->compiler = $compiler;
}










public function get($path, array $data = [])
{
$this->lastCompiled[] = $path;




if (! isset($this->compiledOrNotExpired[$path]) && $this->compiler->isExpired($path)) {
$this->compiler->compile($path);
}





try {
$results = $this->evaluatePath($this->compiler->getCompiledPath($path), $data);
} catch (ViewException $e) {
if (! Str::of($e->getMessage())->contains(['No such file or directory', 'File does not exist at path'])) {
throw $e;
}

if (! isset($this->compiledOrNotExpired[$path])) {
throw $e;
}

$this->compiler->compile($path);

$results = $this->evaluatePath($this->compiler->getCompiledPath($path), $data);
}

$this->compiledOrNotExpired[$path] = true;

array_pop($this->lastCompiled);

return $results;
}










protected function handleViewException(Throwable $e, $obLevel)
{
if ($e instanceof HttpException ||
$e instanceof HttpResponseException ||
$e instanceof RecordNotFoundException ||
$e instanceof RecordsNotFoundException) {
parent::handleViewException($e, $obLevel);
}

$e = new ViewException($this->getMessage($e), 0, 1, $e->getFile(), $e->getLine(), $e);

parent::handleViewException($e, $obLevel);
}







protected function getMessage(Throwable $e)
{
return $e->getMessage().' (View: '.realpath(last($this->lastCompiled)).')';
}






public function getCompiler()
{
return $this->compiler;
}






public function forgetCompiledOrNotExpired()
{
$this->compiledOrNotExpired = [];
}
}
