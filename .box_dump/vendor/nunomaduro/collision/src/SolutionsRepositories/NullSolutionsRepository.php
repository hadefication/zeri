<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\SolutionsRepositories;

use NunoMaduro\Collision\Contracts\SolutionsRepository;
use Throwable;




final class NullSolutionsRepository implements SolutionsRepository
{



public function getFromThrowable(Throwable $throwable): array 
{
return [];
}
}
