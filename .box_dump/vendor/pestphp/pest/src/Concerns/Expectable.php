<?php

declare(strict_types=1);

namespace Pest\Concerns;

use Pest\Expectation;




trait Expectable
{
/**
@template





*/
public function expect(mixed $value): Expectation
{
return new Expectation($value);
}
}
