<?php

namespace Illuminate\View\Compilers\Concerns;

trait CompilesLayouts
{





protected $lastSection;







protected function compileExtends($expression)
{
$expression = $this->stripParentheses($expression);

$echo = "<?php echo \$__env->make({$expression}, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>";

$this->footer[] = $echo;

return '';
}







protected function compileExtendsFirst($expression)
{
$expression = $this->stripParentheses($expression);

$echo = "<?php echo \$__env->first({$expression}, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>";

$this->footer[] = $echo;

return '';
}







protected function compileSection($expression)
{
$this->lastSection = trim($expression, "()'\" ");

return "<?php \$__env->startSection{$expression}; ?>";
}






protected function compileParent()
{
$escapedLastSection = strtr($this->lastSection, ['\\' => '\\\\', "'" => "\\'"]);

return "<?php echo \Illuminate\View\Factory::parentPlaceholder('{$escapedLastSection}'); ?>";
}







protected function compileYield($expression)
{
return "<?php echo \$__env->yieldContent{$expression}; ?>";
}






protected function compileShow()
{
return '<?php echo $__env->yieldSection(); ?>';
}






protected function compileAppend()
{
return '<?php $__env->appendSection(); ?>';
}






protected function compileOverwrite()
{
return '<?php $__env->stopSection(true); ?>';
}






protected function compileStop()
{
return '<?php $__env->stopSection(); ?>';
}






protected function compileEndsection()
{
return '<?php $__env->stopSection(); ?>';
}
}
