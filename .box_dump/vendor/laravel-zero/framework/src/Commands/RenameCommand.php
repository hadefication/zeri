<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\text;
use function sprintf;

final class RenameCommand extends Command
{



protected $signature = 'app:rename {name? : The new name}';




protected $description = 'Set the application name';




public function handle()
{
$this->info('Renaming the application...');

$this->rename();
}





private function rename(): RenameCommand
{
$name = $this->asksForApplicationName();

if (File::exists($this->app->basePath($name))) {
$this->app->abort(403, 'Folder or file already exists.');
} else {
$this->renameBinary($name)
->updateComposer($name);
}

return $this;
}






private function asksForApplicationName(): string
{
if (empty($name = $this->input->getArgument('name'))) {
$name = text('What is your application name?');
}

if (empty($name)) {
$name = trim(basename($this->app->basePath()));
}

return Str::lower($name);
}




private function updateComposer(string $name): RenameCommand
{
$this->task(
'Updating config/app.php "name" property',
function () use ($name) {
$neededLine = "'name' => '".Str::ucfirst($this->getCurrentBinaryName())."'";

if (! Str::contains($contents = $this->getConfig(), $neededLine)) {
return false;
}
File::put(
$this->app->configPath('app.php'),
Str::replaceFirst(
$neededLine,
"'name' => '".Str::ucfirst($name)."'",
$contents
)
);
}
);

$this->task(
'Updating composer "bin"',
function () use ($name) {
$neededLine = '"bin": ["'.$this->getCurrentBinaryName().'"]';

if (! Str::contains($contents = $this->getComposer(), $neededLine)) {
return false;
}

File::put(
$this->app->basePath('composer.json'),
Str::replaceFirst(
$neededLine,
'"bin": ["'.$name.'"]',
$contents
)
);
}
);

return $this;
}




private function renameBinary(string $name): RenameCommand
{
$this->task(
sprintf('Renaming application to "%s"', $name),
function () use ($name) {
return File::move($this->app->basePath($this->getCurrentBinaryName()), $this->app->basePath($name));
}
);

return $this;
}




private function getCurrentBinaryName(): string
{
$composer = $this->getComposer();

return current(@json_decode($composer)->bin);
}




private function getComposer(): string
{
$filePath = $this->app->basePath('composer.json');

if (! File::exists($filePath)) {
$this->app->abort(400, 'The file composer.json not found');
}

return File::get($filePath);
}




private function getConfig(): string
{
$filePath = $this->app->configPath('app.php');

if (! File::exists($filePath)) {
$this->app->abort(400, 'The file config/app.php not found');
}

return File::get($filePath);
}
}
