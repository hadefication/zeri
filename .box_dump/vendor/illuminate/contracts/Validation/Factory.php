<?php

namespace Illuminate\Contracts\Validation;

interface Factory
{









public function make(array $data, array $rules, array $messages = [], array $attributes = []);









public function extend($rule, $extension, $message = null);









public function extendImplicit($rule, $extension, $message = null);








public function replacer($rule, $replacer);
}
