<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Encryption\Encrypter;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'key:generate')]
class KeyGenerateCommand extends Command
{
use ConfirmableTrait;






protected $signature = 'key:generate
                    {--show : Display the key instead of modifying files}
                    {--force : Force the operation to run when in production}';






protected $description = 'Set the application key';






public function handle()
{
$key = $this->generateRandomKey();

if ($this->option('show')) {
return $this->line('<comment>'.$key.'</comment>');
}




if (! $this->setKeyInEnvironmentFile($key)) {
return;
}

$this->laravel['config']['app.key'] = $key;

$this->components->info('Application key set successfully.');
}






protected function generateRandomKey()
{
return 'base64:'.base64_encode(
Encrypter::generateKey($this->laravel['config']['app.cipher'])
);
}







protected function setKeyInEnvironmentFile($key)
{
$currentKey = $this->laravel['config']['app.key'];

if (strlen($currentKey) !== 0 && (! $this->confirmToProceed())) {
return false;
}

if (! $this->writeNewEnvironmentFileWith($key)) {
return false;
}

return true;
}







protected function writeNewEnvironmentFileWith($key)
{
$replaced = preg_replace(
$this->keyReplacementPattern(),
'APP_KEY='.$key,
$input = file_get_contents($this->laravel->environmentFilePath())
);

if ($replaced === $input || $replaced === null) {
$this->error('Unable to set application key. No APP_KEY variable was found in the .env file.');

return false;
}

file_put_contents($this->laravel->environmentFilePath(), $replaced);

return true;
}






protected function keyReplacementPattern()
{
$escaped = preg_quote('='.$this->laravel['config']['app.key'], '/');

return "/^APP_KEY{$escaped}/m";
}
}
