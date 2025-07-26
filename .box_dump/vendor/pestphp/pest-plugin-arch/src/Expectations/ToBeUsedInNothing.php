<?php

declare(strict_types=1);

namespace Pest\Arch\Expectations;

use Pest\Arch\Contracts\ArchExpectation;
use Pest\Expectation;




final class ToBeUsedInNothing
{





public static function make(Expectation $expectation): ArchExpectation
{
return ToOnlyBeUsedIn::make($expectation, []);
}
}
