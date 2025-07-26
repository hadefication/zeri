<?php

namespace Illuminate\View\Compilers\Concerns;

use Illuminate\Foundation\Vite;

trait CompilesHelpers
{





protected function compileCsrf()
{
return '<?php echo csrf_field(); ?>';
}







protected function compileDd($arguments)
{
return "<?php dd{$arguments}; ?>";
}







protected function compileDump($arguments)
{
return "<?php dump{$arguments}; ?>";
}







protected function compileMethod($method)
{
return "<?php echo method_field{$method}; ?>";
}







protected function compileVite($arguments)
{
$arguments ??= '()';

$class = Vite::class;

return "<?php echo app('$class'){$arguments}; ?>";
}






protected function compileViteReactRefresh()
{
$class = Vite::class;

return "<?php echo app('$class')->reactRefresh(); ?>";
}
}
