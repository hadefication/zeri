<?php

namespace Illuminate\Foundation\Console;

use Carbon\CarbonInterval;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\Client\Factory as Http;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Env;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;

use function Laravel\Prompts\suggest;

#[AsCommand(name: 'docs')]
class DocsCommand extends Command
{





protected $signature = 'docs {page? : The documentation page to open} {section? : The section of the page to open}';






protected $description = 'Access the Laravel documentation';






protected $help = 'If you would like to perform a content search against the documentation, you may call: <fg=green>php artisan docs -- </><fg=green;options=bold;>search query here</>';






protected $http;






protected $cache;






protected $urlOpener;






protected $version;






protected $systemOsFamily = PHP_OS_FAMILY;






protected function configure()
{
parent::configure();

if ($this->isSearching()) {
$this->ignoreValidationErrors();
}
}








public function handle(Http $http, Cache $cache)
{
$this->http = $http;
$this->cache = $cache;

try {
$this->openUrl();
} catch (ProcessFailedException $e) {
if ($e->getProcess()->getExitCodeText() === 'Interrupt') {
return $e->getProcess()->getExitCode();
}

throw $e;
}

$this->refreshDocs();

return Command::SUCCESS;
}






protected function openUrl()
{
with($this->url(), function ($url) {
$this->components->info("Opening the docs to: <fg=yellow>{$url}</>");

$this->open($url);
});
}






protected function url()
{
if ($this->isSearching()) {
return "https://laravel.com/docs/{$this->version()}?".Arr::query([
'q' => $this->searchQuery(),
]);
}

return with($this->page(), function ($page) {
return trim("https://laravel.com/docs/{$this->version()}/{$page}#{$this->section($page)}", '#/');
});
}






protected function page()
{
return with($this->resolvePage(), function ($page) {
if ($page === null) {
$this->components->warn('Unable to determine the page you are trying to visit.');

return '/';
}

return $page;
});
}






protected function resolvePage()
{
if ($this->option('no-interaction') && $this->didNotRequestPage()) {
return '/';
}

return $this->didNotRequestPage()
? $this->askForPage()
: $this->guessPage($this->argument('page'));
}






protected function didNotRequestPage()
{
return $this->argument('page') === null;
}






protected function askForPage()
{
return $this->askForPageViaCustomStrategy() ?? $this->askForPageViaAutocomplete();
}






protected function askForPageViaCustomStrategy()
{
try {
$strategy = require Env::get('ARTISAN_DOCS_ASK_STRATEGY');
} catch (Throwable) {
return null;
}

if (! is_callable($strategy)) {
return null;
}

return $strategy($this) ?? '/';
}






protected function askForPageViaAutocomplete()
{
$choice = suggest(
label: 'Which page would you like to open?',
options: fn ($value) => $this->pages()
->mapWithKeys(fn ($option) => [
Str::lower($option['title']) => $option['title'],
])
->filter(fn ($title) => str_contains(Str::lower($title), Str::lower($value)))
->all(),
placeholder: 'E.g. Collections'
);

return $this->pages()->filter(
fn ($page) => $page['title'] === $choice || Str::lower($page['title']) === $choice
)->keys()->first() ?: $this->guessPage($choice);
}






protected function guessPage($search)
{
return $this->pages()
->filter(fn ($page) => str_starts_with(
Str::slug($page['title'], ' '),
Str::slug($search, ' ')
))->keys()->first() ?? $this->pages()->map(fn ($page) => similar_text(
Str::slug($page['title'], ' '),
Str::slug($search, ' '),
))
->filter(fn ($score) => $score >= min(3, Str::length($search)))
->sortDesc()
->keys()
->sortByDesc(fn ($slug) => Str::contains(
Str::slug($this->pages()[$slug]['title'], ' '),
Str::slug($search, ' ')
) ? 1 : 0)
->first();
}







protected function section($page)
{
return $this->didNotRequestSection()
? null
: $this->guessSection($page);
}






protected function didNotRequestSection()
{
return $this->argument('section') === null;
}







protected function guessSection($page)
{
return $this->sectionsFor($page)
->filter(fn ($section) => str_starts_with(
Str::slug($section['title'], ' '),
Str::slug($this->argument('section'), ' ')
))->keys()->first() ?? $this->sectionsFor($page)->map(fn ($section) => similar_text(
Str::slug($section['title'], ' '),
Str::slug($this->argument('section'), ' '),
))
->filter(fn ($score) => $score >= min(3, Str::length($this->argument('section'))))
->sortDesc()
->keys()
->sortByDesc(fn ($slug) => Str::contains(
Str::slug($this->sectionsFor($page)[$slug]['title'], ' '),
Str::slug($this->argument('section'), ' ')
) ? 1 : 0)
->first();
}







protected function open($url)
{
($this->urlOpener ?? function ($url) {
if (Env::get('ARTISAN_DOCS_OPEN_STRATEGY')) {
$this->openViaCustomStrategy($url);
} elseif (in_array($this->systemOsFamily, ['Darwin', 'Windows', 'Linux'])) {
$this->openViaBuiltInStrategy($url);
} else {
$this->components->warn('Unable to open the URL on your system. You will need to open it yourself or create a custom opener for your system.');
}
})($url);
}







protected function openViaCustomStrategy($url)
{
try {
$command = require Env::get('ARTISAN_DOCS_OPEN_STRATEGY');
} catch (Throwable) {
$command = null;
}

if (! is_callable($command)) {
$this->components->warn('Unable to open the URL with your custom strategy. You will need to open it yourself.');

return;
}

$command($url);
}







protected function openViaBuiltInStrategy($url)
{
if ($this->systemOsFamily === 'Windows') {
$process = tap(Process::fromShellCommandline(escapeshellcmd("start {$url}")))->run();

if (! $process->isSuccessful()) {
throw new ProcessFailedException($process);
}

return;
}

$binary = (new Collection(match ($this->systemOsFamily) {
'Darwin' => ['open'],
'Linux' => ['xdg-open', 'wslview'],
}))->first(fn ($binary) => (new ExecutableFinder)->find($binary) !== null);

if ($binary === null) {
$this->components->warn('Unable to open the URL on your system. You will need to open it yourself or create a custom opener for your system.');

return;
}

$process = tap(Process::fromShellCommandline(escapeshellcmd("{$binary} {$url}")))->run();

if (! $process->isSuccessful()) {
throw new ProcessFailedException($process);
}
}







public function sectionsFor($page)
{
return new Collection($this->pages()[$page]['sections']);
}






public function pages()
{
return new Collection($this->docs()['pages']);
}






public function docs()
{
return $this->cache->remember(
"artisan.docs.{{$this->version()}}.index",
CarbonInterval::months(2),
fn () => $this->fetchDocs()->throw()->collect()
);
}






protected function refreshDocs()
{
with($this->fetchDocs(), function ($response) {
if ($response->successful()) {
$this->cache->put("artisan.docs.{{$this->version()}}.index", $response->collect(), CarbonInterval::months(2));
}
});
}






protected function fetchDocs()
{
return $this->http->get("https://laravel.com/docs/{$this->version()}/index.json");
}






protected function version()
{
return Str::before($this->version ?? $this->laravel->version(), '.').'.x';
}






protected function searchQuery()
{
return (new Collection($_SERVER['argv']))->skip(3)->implode(' ');
}






protected function isSearching()
{
return ($_SERVER['argv'][2] ?? null) === '--';
}







public function setVersion($version)
{
$this->version = $version;

return $this;
}







public function setUrlOpener($opener)
{
$this->urlOpener = $opener;

return $this;
}







public function setSystemOsFamily($family)
{
$this->systemOsFamily = $family;

return $this;
}
}
