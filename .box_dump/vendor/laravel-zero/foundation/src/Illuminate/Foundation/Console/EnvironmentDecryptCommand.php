<?php

namespace Illuminate\Foundation\Console;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\password;

#[AsCommand(name: 'env:decrypt')]
class EnvironmentDecryptCommand extends Command
{





protected $signature = 'env:decrypt
                    {--key= : The encryption key}
                    {--cipher= : The encryption cipher}
                    {--env= : The environment to be decrypted}
                    {--force : Overwrite the existing environment file}
                    {--path= : Path to write the decrypted file}
                    {--filename= : Filename of the decrypted file}';






protected $description = 'Decrypt an environment file';






protected $files;






public function __construct(Filesystem $files)
{
parent::__construct();

$this->files = $files;
}






public function handle()
{
$key = $this->option('key') ?: Env::get('LARAVEL_ENV_ENCRYPTION_KEY');

if (! $key && $this->input->isInteractive()) {
$key = password('What is the decryption key?');
}

if (! $key) {
$this->fail('A decryption key is required.');
}

$cipher = $this->option('cipher') ?: 'AES-256-CBC';

$key = $this->parseKey($key);

$encryptedFile = ($this->option('env')
? Str::finish(dirname($this->laravel->environmentFilePath()), DIRECTORY_SEPARATOR).'.env.'.$this->option('env')
: $this->laravel->environmentFilePath()).'.encrypted';

$outputFile = $this->outputFilePath();

if (Str::endsWith($outputFile, '.encrypted')) {
$this->fail('Invalid filename.');
}

if (! $this->files->exists($encryptedFile)) {
$this->fail('Encrypted environment file not found.');
}

if ($this->files->exists($outputFile) && ! $this->option('force')) {
$this->fail('Environment file already exists.');
}

try {
$encrypter = new Encrypter($key, $cipher);

$this->files->put(
$outputFile,
$encrypter->decrypt($this->files->get($encryptedFile))
);
} catch (Exception $e) {
$this->fail($e->getMessage());
}

$this->components->info('Environment successfully decrypted.');

$this->components->twoColumnDetail('Decrypted file', $outputFile);

$this->newLine();
}







protected function parseKey(string $key)
{
if (Str::startsWith($key, $prefix = 'base64:')) {
$key = base64_decode(Str::after($key, $prefix));
}

return $key;
}






protected function outputFilePath()
{
$path = Str::finish($this->option('path') ?: dirname($this->laravel->environmentFilePath()), DIRECTORY_SEPARATOR);

$outputFile = $this->option('filename') ?: ('.env'.($this->option('env') ? '.'.$this->option('env') : ''));
$outputFile = ltrim($outputFile, DIRECTORY_SEPARATOR);

return $path.$outputFile;
}
}
