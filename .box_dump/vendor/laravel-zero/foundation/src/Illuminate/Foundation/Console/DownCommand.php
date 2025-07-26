<?php

namespace Illuminate\Foundation\Console;

use App\Http\Middleware\PreventRequestsDuringMaintenance;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Foundation\Events\MaintenanceModeEnabled;
use Illuminate\Foundation\Exceptions\RegisterErrorViewPaths;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(name: 'down')]
class DownCommand extends Command
{





protected $signature = 'down {--redirect= : The path that users should be redirected to}
                                 {--render= : The view that should be prerendered for display during maintenance mode}
                                 {--retry= : The number of seconds after which the request may be retried}
                                 {--refresh= : The number of seconds after which the browser may refresh}
                                 {--secret= : The secret phrase that may be used to bypass maintenance mode}
                                 {--with-secret : Generate a random secret phrase that may be used to bypass maintenance mode}
                                 {--status=503 : The status code that should be used when returning the maintenance mode response}';






protected $description = 'Put the application into maintenance / demo mode';






public function handle()
{
try {
if ($this->laravel->maintenanceMode()->active() && ! $this->getSecret()) {
$this->components->info('Application is already down.');

return 0;
}

$downFilePayload = $this->getDownFilePayload();

$this->laravel->maintenanceMode()->activate($downFilePayload);

file_put_contents(
storage_path('framework/maintenance.php'),
file_get_contents(__DIR__.'/stubs/maintenance-mode.stub')
);

$this->laravel->get('events')->dispatch(new MaintenanceModeEnabled());

$this->components->info('Application is now in maintenance mode.');

if ($downFilePayload['secret'] !== null) {
$this->components->info('You may bypass maintenance mode via ['.config('app.url')."/{$downFilePayload['secret']}].");
}
} catch (Exception $e) {
$this->components->error(sprintf(
'Failed to enter maintenance mode: %s.',
$e->getMessage(),
));

return 1;
}
}






protected function getDownFilePayload()
{
return [
'except' => $this->excludedPaths(),
'redirect' => $this->redirectPath(),
'retry' => $this->getRetryTime(),
'refresh' => $this->option('refresh'),
'secret' => $this->getSecret(),
'status' => (int) ($this->option('status') ?? 503),
'template' => $this->option('render') ? $this->prerenderView() : null,
];
}






protected function excludedPaths()
{
try {
return $this->laravel->make(PreventRequestsDuringMaintenance::class)->getExcludedPaths();
} catch (Throwable) {
return [];
}
}






protected function redirectPath()
{
if ($this->option('redirect') && $this->option('redirect') !== '/') {
return '/'.trim($this->option('redirect'), '/');
}

return $this->option('redirect');
}






protected function prerenderView()
{
(new RegisterErrorViewPaths)();

return view($this->option('render'), [
'retryAfter' => $this->option('retry'),
])->render();
}






protected function getRetryTime()
{
$retry = $this->option('retry');

return is_numeric($retry) && $retry > 0 ? (int) $retry : null;
}






protected function getSecret()
{
return match (true) {
! is_null($this->option('secret')) => $this->option('secret'),
$this->option('with-secret') => Str::random(),
default => null,
};
}
}
