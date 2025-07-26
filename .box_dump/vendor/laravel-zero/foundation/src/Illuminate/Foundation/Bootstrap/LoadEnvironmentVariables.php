<?php

namespace Illuminate\Foundation\Bootstrap;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Env;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class LoadEnvironmentVariables
{






public function bootstrap(Application $app)
{
if ($app->configurationIsCached()) {
return;
}

$this->checkForSpecificEnvironmentFile($app);

try {
$this->createDotenv($app)->safeLoad();
} catch (InvalidFileException $e) {
$this->writeErrorAndDie($e);
}
}







protected function checkForSpecificEnvironmentFile($app)
{
if ($app->runningInConsole() &&
($input = new ArgvInput)->hasParameterOption('--env') &&
$this->setEnvironmentFilePath($app, $app->environmentFile().'.'.$input->getParameterOption('--env'))) {
return;
}

$environment = Env::get('APP_ENV');

if (! $environment) {
return;
}

$this->setEnvironmentFilePath(
$app, $app->environmentFile().'.'.$environment
);
}








protected function setEnvironmentFilePath($app, $file)
{
if (is_file($app->environmentPath().'/'.$file)) {
$app->loadEnvironmentFrom($file);

return true;
}

return false;
}







protected function createDotenv($app)
{
return Dotenv::create(
Env::getRepository(),
$app->environmentPath(),
$app->environmentFile()
);
}







protected function writeErrorAndDie(InvalidFileException $e)
{
$output = (new ConsoleOutput)->getErrorOutput();

$output->writeln('The environment file is invalid!');
$output->writeln($e->getMessage());

http_response_code(500);

exit(1);
}
}
