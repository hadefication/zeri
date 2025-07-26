<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'config:show')]
class ConfigShowCommand extends Command
{





protected $signature = 'config:show {config : The configuration file or key to show}';






protected $description = 'Display all of the values for a given configuration file or key';






public function handle()
{
$config = $this->argument('config');

if (! config()->has($config)) {
$this->fail("Configuration file or key <comment>{$config}</comment> does not exist.");
}

$this->newLine();
$this->render($config);
$this->newLine();

return Command::SUCCESS;
}







public function render($name)
{
$data = config($name);

if (! is_array($data)) {
$this->title($name, $this->formatValue($data));

return;
}

$this->title($name);

foreach (Arr::dot($data) as $key => $value) {
$this->components->twoColumnDetail(
$this->formatKey($key),
$this->formatValue($value)
);
}
}








public function title($title, $subtitle = null)
{
$this->components->twoColumnDetail(
"<fg=green;options=bold>{$title}</>",
$subtitle,
);
}







protected function formatKey($key)
{
return preg_replace_callback(
'/(.*)\.(.*)$/', fn ($matches) => sprintf(
'<fg=gray>%s ⇁</> %s',
str_replace('.', ' ⇁ ', $matches[1]),
$matches[2]
), $key
);
}







protected function formatValue($value)
{
return match (true) {
is_bool($value) => sprintf('<fg=#ef8414;options=bold>%s</>', $value ? 'true' : 'false'),
is_null($value) => '<fg=#ef8414;options=bold>null</>',
is_numeric($value) => "<fg=#ef8414;options=bold>{$value}</>",
is_array($value) => '[]',
is_object($value) => get_class($value),
is_string($value) => $value,
default => print_r($value, true),
};
}
}
