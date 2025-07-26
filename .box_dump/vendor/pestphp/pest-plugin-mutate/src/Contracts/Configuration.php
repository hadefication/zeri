<?php

declare(strict_types=1);

namespace Pest\Mutate\Contracts;

interface Configuration
{



public function path(array|string ...$paths): self;




public function ignore(array|string ...$paths): self;




public function mutator(array|string ...$mutators): self;




public function except(array|string ...$mutators): self;

public function min(float $minScore, ?bool $failOnZeroMutations = null): self;

public function ignoreMinScoreOnZeroMutations(bool $ignore = true): self;

public function coveredOnly(bool $coveredOnly = true): self;

public function parallel(bool $parallel = true): self;

public function processes(?int $processes = null): self;

public function profile(bool $profile = true): self;

public function stopOnUntested(bool $stopOnUntested = true): self;

public function stopOnUncovered(bool $stopOnUncovered = true): self;

public function bail(): self;




public function class(array|string ...$classes): self;

public function retry(bool $retry = true): self;
}
