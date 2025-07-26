<?php

namespace Illuminate\Contracts\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;

interface CastsInboundAttributes
{









public function set(Model $model, string $key, mixed $value, array $attributes);
}
