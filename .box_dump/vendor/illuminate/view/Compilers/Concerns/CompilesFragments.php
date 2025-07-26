<?php

namespace Illuminate\View\Compilers\Concerns;

trait CompilesFragments
{





protected $lastFragment;







protected function compileFragment($expression)
{
$this->lastFragment = trim($expression, "()'\" ");

return "<?php \$__env->startFragment{$expression}; ?>";
}






protected function compileEndfragment()
{
return '<?php echo $__env->stopFragment(); ?>';
}
}
