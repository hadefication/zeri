<?php

namespace Illuminate\Console\Events;

use Illuminate\Console\Scheduling\Event;

class ScheduledBackgroundTaskFinished
{





public function __construct(
public Event $task,
) {
}
}
