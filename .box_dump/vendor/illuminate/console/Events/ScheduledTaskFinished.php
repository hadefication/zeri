<?php

namespace Illuminate\Console\Events;

use Illuminate\Console\Scheduling\Event;

class ScheduledTaskFinished
{






public function __construct(
public Event $task,
public float $runtime,
) {
}
}
