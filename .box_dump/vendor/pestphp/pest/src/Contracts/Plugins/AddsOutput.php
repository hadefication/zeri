<?php

declare(strict_types=1);

namespace Pest\Contracts\Plugins;




interface AddsOutput
{



public function addOutput(int $exitCode): int;
}
