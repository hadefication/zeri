<?php

declare(strict_types=1);

namespace Pest\Plugins;

use Composer\InstalledVersions;
use Pest\Console\Thanks;
use Pest\Contracts\Plugins\HandlesArguments;
use Pest\Support\View;
use Pest\TestSuite;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;




final readonly class Init implements HandlesArguments
{



private const INIT_OPTION = '--init';




private const STUBS = [
'phpunit.xml.stub' => 'phpunit.xml',
'Pest.php.stub' => 'tests/Pest.php',
'TestCase.php.stub' => 'tests/TestCase.php',
'Unit/ExampleTest.php.stub' => 'tests/Unit/ExampleTest.php',
'Feature/ExampleTest.php.stub' => 'tests/Feature/ExampleTest.php',
];




public function __construct(
private TestSuite $testSuite,
private InputInterface $input,
private OutputInterface $output
) {

}




public function handleArguments(array $arguments): array
{
if (! array_key_exists(1, $arguments)) {
return $arguments;
}
if ($arguments[1] !== self::INIT_OPTION) {
return $arguments;
}

unset($arguments[1]);

$this->init();

exit(0);
}




public function init(): void
{
$testsBaseDir = "{$this->testSuite->rootPath}/tests";

if (! is_dir($testsBaseDir)) {
mkdir($testsBaseDir);
}

View::render('components.badge', [
'type' => 'INFO',
'content' => 'Preparing tests directory.',
]);

foreach (self::STUBS as $from => $to) {
if ($this->isLaravelInstalled()) {
$fromPath = __DIR__."/../../stubs/init-laravel/{$from}";
} else {
$fromPath = __DIR__."/../../stubs/init/{$from}";
}

$toPath = "{$this->testSuite->rootPath}/{$to}";

if (file_exists($toPath)) {
View::render('components.two-column-detail', [
'left' => $to,
'right' => 'File already exists.',
]);

continue;
}

if (! is_dir(dirname($toPath))) {
mkdir(dirname($toPath));
}

copy($fromPath, $toPath);

View::render('components.two-column-detail', [
'left' => $to,
'right' => 'File created.',
]);
}

View::render('components.new-line');

(new Thanks($this->input, $this->output))();
}




private function isLaravelInstalled(): bool
{
return InstalledVersions::isInstalled('laravel/framework');
}
}
