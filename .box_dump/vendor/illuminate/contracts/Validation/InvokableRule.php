<?php

namespace Illuminate\Contracts\Validation;

use Closure;




interface InvokableRule
{








public function __invoke(string $attribute, mixed $value, Closure $fail);
}
