<?php

namespace Illuminate\View\Compilers\Concerns;

use Illuminate\Support\Str;

trait CompilesStacks
{






protected function compileStack($expression)
{
return "<?php echo \$__env->yieldPushContent{$expression}; ?>";
}







protected function compilePush($expression)
{
return "<?php \$__env->startPush{$expression}; ?>";
}







protected function compilePushOnce($expression)
{
$parts = explode(',', $this->stripParentheses($expression), 2);

[$stack, $id] = [$parts[0], $parts[1] ?? ''];

$id = trim($id) ?: "'".(string) Str::uuid()."'";

return '<?php if (! $__env->hasRenderedOnce('.$id.')): $__env->markAsRenderedOnce('.$id.');
$__env->startPush('.$stack.'); ?>';
}






protected function compileEndpush()
{
return '<?php $__env->stopPush(); ?>';
}






protected function compileEndpushOnce()
{
return '<?php $__env->stopPush(); endif; ?>';
}







protected function compilePrepend($expression)
{
return "<?php \$__env->startPrepend{$expression}; ?>";
}







protected function compilePrependOnce($expression)
{
$parts = explode(',', $this->stripParentheses($expression), 2);

[$stack, $id] = [$parts[0], $parts[1] ?? ''];

$id = trim($id) ?: "'".(string) Str::uuid()."'";

return '<?php if (! $__env->hasRenderedOnce('.$id.')): $__env->markAsRenderedOnce('.$id.');
$__env->startPrepend('.$stack.'); ?>';
}






protected function compileEndprepend()
{
return '<?php $__env->stopPrepend(); ?>';
}






protected function compileEndprependOnce()
{
return '<?php $__env->stopPrepend(); endif; ?>';
}
}
