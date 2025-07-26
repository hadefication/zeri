<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Laravel;

use NunoMaduro\Collision\Contracts\SolutionsRepository;
use Spatie\ErrorSolutions\Contracts\SolutionProviderRepository;
use Spatie\Ignition\Contracts\SolutionProviderRepository as IgnitionSolutionProviderRepository;
use Throwable;




final class IgnitionSolutionsRepository implements SolutionsRepository
{





protected $solutionProviderRepository; 




public function __construct(IgnitionSolutionProviderRepository|SolutionProviderRepository $solutionProviderRepository) 
{
$this->solutionProviderRepository = $solutionProviderRepository;
}




public function getFromThrowable(Throwable $throwable): array 
{
return $this->solutionProviderRepository->getSolutionsForThrowable($throwable); 
}
}
