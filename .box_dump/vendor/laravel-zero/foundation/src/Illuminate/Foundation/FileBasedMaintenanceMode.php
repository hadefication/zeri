<?php

namespace Illuminate\Foundation;

use Illuminate\Contracts\Foundation\MaintenanceMode as MaintenanceModeContract;

class FileBasedMaintenanceMode implements MaintenanceModeContract
{






public function activate(array $payload): void
{
file_put_contents(
$this->path(),
json_encode($payload, JSON_PRETTY_PRINT)
);
}






public function deactivate(): void
{
if ($this->active()) {
unlink($this->path());
}
}






public function active(): bool
{
return file_exists($this->path());
}






public function data(): array
{
return json_decode(file_get_contents($this->path()), true);
}






protected function path(): string
{
return storage_path('framework/down');
}
}
