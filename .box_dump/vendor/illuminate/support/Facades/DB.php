<?php

namespace Illuminate\Support\Facades;

use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Console\Migrations\RefreshCommand;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Console\WipeCommand;














































































































class DB extends Facade
{








public static function prohibitDestructiveCommands(bool $prohibit = true)
{
FreshCommand::prohibit($prohibit);
RefreshCommand::prohibit($prohibit);
ResetCommand::prohibit($prohibit);
RollbackCommand::prohibit($prohibit);
WipeCommand::prohibit($prohibit);
}






protected static function getFacadeAccessor()
{
return 'db';
}
}
