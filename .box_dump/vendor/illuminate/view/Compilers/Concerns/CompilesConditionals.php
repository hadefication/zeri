<?php

namespace Illuminate\View\Compilers\Concerns;

use Illuminate\Support\Str;

trait CompilesConditionals
{





protected $firstCaseInSwitch = true;







protected function compileAuth($guard = null)
{
$guard = is_null($guard) ? '()' : $guard;

return "<?php if(auth()->guard{$guard}->check()): ?>";
}







protected function compileElseAuth($guard = null)
{
$guard = is_null($guard) ? '()' : $guard;

return "<?php elseif(auth()->guard{$guard}->check()): ?>";
}






protected function compileEndAuth()
{
return '<?php endif; ?>';
}







protected function compileEnv($environments)
{
return "<?php if(app()->environment{$environments}): ?>";
}






protected function compileEndEnv()
{
return '<?php endif; ?>';
}






protected function compileProduction()
{
return "<?php if(app()->environment('production')): ?>";
}






protected function compileEndProduction()
{
return '<?php endif; ?>';
}







protected function compileGuest($guard = null)
{
$guard = is_null($guard) ? '()' : $guard;

return "<?php if(auth()->guard{$guard}->guest()): ?>";
}







protected function compileElseGuest($guard = null)
{
$guard = is_null($guard) ? '()' : $guard;

return "<?php elseif(auth()->guard{$guard}->guest()): ?>";
}






protected function compileEndGuest()
{
return '<?php endif; ?>';
}







protected function compileHasSection($expression)
{
return "<?php if (! empty(trim(\$__env->yieldContent{$expression}))): ?>";
}







protected function compileSectionMissing($expression)
{
return "<?php if (empty(trim(\$__env->yieldContent{$expression}))): ?>";
}







protected function compileIf($expression)
{
return "<?php if{$expression}: ?>";
}







protected function compileUnless($expression)
{
return "<?php if (! {$expression}): ?>";
}







protected function compileElseif($expression)
{
return "<?php elseif{$expression}: ?>";
}






protected function compileElse()
{
return '<?php else: ?>';
}






protected function compileEndif()
{
return '<?php endif; ?>';
}






protected function compileEndunless()
{
return '<?php endif; ?>';
}







protected function compileIsset($expression)
{
return "<?php if(isset{$expression}): ?>";
}






protected function compileEndIsset()
{
return '<?php endif; ?>';
}







protected function compileSwitch($expression)
{
$this->firstCaseInSwitch = true;

return "<?php switch{$expression}:";
}







protected function compileCase($expression)
{
if ($this->firstCaseInSwitch) {
$this->firstCaseInSwitch = false;

return "case {$expression}: ?>";
}

return "<?php case {$expression}: ?>";
}






protected function compileDefault()
{
return '<?php default: ?>';
}






protected function compileEndSwitch()
{
return '<?php endswitch; ?>';
}







protected function compileOnce($id = null)
{
$id = $id ? $this->stripParentheses($id) : "'".(string) Str::uuid()."'";

return '<?php if (! $__env->hasRenderedOnce('.$id.')): $__env->markAsRenderedOnce('.$id.'); ?>';
}






public function compileEndOnce()
{
return '<?php endif; ?>';
}







protected function compileBool($condition)
{
return "<?php echo ($condition ? 'true' : 'false'); ?>";
}







protected function compileChecked($condition)
{
return "<?php if{$condition}: echo 'checked'; endif; ?>";
}







protected function compileDisabled($condition)
{
return "<?php if{$condition}: echo 'disabled'; endif; ?>";
}







protected function compileRequired($condition)
{
return "<?php if{$condition}: echo 'required'; endif; ?>";
}







protected function compileReadonly($condition)
{
return "<?php if{$condition}: echo 'readonly'; endif; ?>";
}







protected function compileSelected($condition)
{
return "<?php if{$condition}: echo 'selected'; endif; ?>";
}







protected function compilePushIf($expression)
{
$parts = explode(',', $this->stripParentheses($expression), 2);

return "<?php if({$parts[0]}): \$__env->startPush({$parts[1]}); ?>";
}







protected function compileElsePushIf($expression)
{
$parts = explode(',', $this->stripParentheses($expression), 2);

return "<?php \$__env->stopPush(); elseif({$parts[0]}): \$__env->startPush({$parts[1]}); ?>";
}







protected function compileElsePush($expression)
{
return "<?php \$__env->stopPush(); else: \$__env->startPush{$expression}; ?>";
}






protected function compileEndPushIf()
{
return '<?php $__env->stopPush(); endif; ?>';
}
}
