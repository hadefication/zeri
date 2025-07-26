<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Contracts;

use Spatie\Ignition\Contracts\Solution;
use Throwable;




interface SolutionsRepository
{





public function getFromThrowable(Throwable $throwable): array; 
}
