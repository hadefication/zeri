<?php

namespace Illuminate\Bus\Events;

use Illuminate\Bus\Batch;

class BatchDispatched
{





public function __construct(
public Batch $batch,
) {
}
}
