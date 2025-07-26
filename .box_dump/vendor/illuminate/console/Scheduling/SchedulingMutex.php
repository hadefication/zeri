<?php

namespace Illuminate\Console\Scheduling;

use DateTimeInterface;

interface SchedulingMutex
{







public function create(Event $event, DateTimeInterface $time);








public function exists(Event $event, DateTimeInterface $time);
}
