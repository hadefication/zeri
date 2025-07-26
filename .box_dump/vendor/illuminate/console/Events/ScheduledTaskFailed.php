<?php

namespace Illuminate\Console\Events;

use Illuminate\Console\Scheduling\Event;
use Throwable;

class ScheduledTaskFailed
{






public function __construct(
public Event $task,
public Throwable $exception,
) {
}
}
