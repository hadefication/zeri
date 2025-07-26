<?php

namespace Illuminate\View\Engines;

use Illuminate\Contracts\View\Engine;
use Illuminate\Filesystem\Filesystem;
use Throwable;

class PhpEngine implements Engine
{





protected $files;






public function __construct(Filesystem $files)
{
$this->files = $files;
}








public function get($path, array $data = [])
{
return $this->evaluatePath($path, $data);
}








protected function evaluatePath($path, $data)
{
$obLevel = ob_get_level();

ob_start();




try {
$this->files->getRequire($path, $data);
} catch (Throwable $e) {
$this->handleViewException($e, $obLevel);
}

return ltrim(ob_get_clean());
}










protected function handleViewException(Throwable $e, $obLevel)
{
while (ob_get_level() > $obLevel) {
ob_end_clean();
}

throw $e;
}
}
