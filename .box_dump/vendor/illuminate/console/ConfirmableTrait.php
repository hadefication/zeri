<?php

namespace Illuminate\Console;

use function Laravel\Prompts\confirm;

trait ConfirmableTrait
{









public function confirmToProceed($warning = 'Application In Production', $callback = null)
{
$callback = is_null($callback) ? $this->getDefaultConfirmCallback() : $callback;

$shouldConfirm = value($callback);

if ($shouldConfirm) {
if ($this->hasOption('force') && $this->option('force')) {
return true;
}

$this->components->alert($warning);

$confirmed = confirm('Are you sure you want to run this command?', default: false);

if (! $confirmed) {
$this->components->warn('Command cancelled.');

return false;
}
}

return true;
}






protected function getDefaultConfirmCallback()
{
return function () {
return $this->getLaravel()->environment() === 'production';
};
}
}
