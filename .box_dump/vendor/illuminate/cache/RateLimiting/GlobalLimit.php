<?php

namespace Illuminate\Cache\RateLimiting;

class GlobalLimit extends Limit
{






public function __construct(int $maxAttempts, int $decaySeconds = 60)
{
parent::__construct('', $maxAttempts, $decaySeconds);
}
}
