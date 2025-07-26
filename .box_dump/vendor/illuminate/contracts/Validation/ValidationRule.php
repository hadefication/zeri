<?php

namespace Illuminate\Contracts\Validation;

use Closure;

interface ValidationRule
{








public function validate(string $attribute, mixed $value, Closure $fail): void;
}
