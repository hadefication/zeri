<?php

namespace Illuminate\Contracts\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;

interface ComparesCastableAttributes
{









public function compare(Model $model, string $key, mixed $firstValue, mixed $secondValue);
}
