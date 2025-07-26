<?php

namespace Illuminate\Contracts\Foundation;

interface MaintenanceMode
{






public function activate(array $payload): void;






public function deactivate(): void;






public function active(): bool;






public function data(): array;
}
