<?php

namespace Illuminate\Console\Scheduling;

use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Facades\Date;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'schedule:interrupt')]
class ScheduleInterruptCommand extends Command
{





protected $name = 'schedule:interrupt';






protected $description = 'Interrupt the current schedule run';






protected $cache;






public function __construct(Cache $cache)
{
parent::__construct();

$this->cache = $cache;
}






public function handle()
{
$this->cache->put('illuminate:schedule:interrupt', true, Date::now()->endOfMinute());

$this->components->info('Broadcasting schedule interrupt signal.');
}
}
