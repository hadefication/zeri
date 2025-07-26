<?php

namespace Illuminate\Console\Events;

use Illuminate\Console\Application;

class ArtisanStarting
{





public function __construct(
public Application $artisan,
) {
}
}
