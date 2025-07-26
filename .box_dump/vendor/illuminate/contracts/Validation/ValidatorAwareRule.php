<?php

namespace Illuminate\Contracts\Validation;

use Illuminate\Validation\Validator;

interface ValidatorAwareRule
{






public function setValidator(Validator $validator);
}
