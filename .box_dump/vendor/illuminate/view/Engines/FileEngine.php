<?php

namespace Illuminate\View\Engines;

use Illuminate\Contracts\View\Engine;
use Illuminate\Filesystem\Filesystem;

class FileEngine implements Engine
{





protected $files;






public function __construct(Filesystem $files)
{
$this->files = $files;
}








public function get($path, array $data = [])
{
return $this->files->get($path);
}
}
