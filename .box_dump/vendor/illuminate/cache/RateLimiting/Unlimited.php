<?php

namespace Illuminate\Cache\RateLimiting;

class Unlimited extends GlobalLimit
{



public function __construct()
{
parent::__construct(PHP_INT_MAX);
}
}
