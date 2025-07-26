<?php

namespace Illuminate\Console\Events;

use Illuminate\Console\Scheduling\Event;

class ScheduledTaskSkipped
{





public function __construct(
public Event $task,
) {
}
}
