<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Events\VendorTagPublished;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\MountManager;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\search;
use function Laravel\Prompts\select;

#[AsCommand(name: 'vendor:publish')]
class VendorPublishCommand extends Command
{





protected $files;






protected $provider = null;






protected $tags = [];






protected $publishedAt;






protected $signature = 'vendor:publish
                    {--existing : Publish and overwrite only the files that have already been published}
                    {--force : Overwrite any existing files}
                    {--all : Publish assets for all service providers without prompt}
                    {--provider= : The service provider that has assets you want to publish}
                    {--tag=* : One or many tags that have assets you want to publish}';






protected $description = 'Publish any publishable assets from vendor packages';






protected static $updateMigrationDates = true;






public function __construct(Filesystem $files)
{
parent::__construct();

$this->files = $files;
}






public function handle()
{
$this->publishedAt = now();

$this->determineWhatShouldBePublished();

foreach ($this->tags ?: [null] as $tag) {
$this->publishTag($tag);
}
}






protected function determineWhatShouldBePublished()
{
if ($this->option('all')) {
return;
}

[$this->provider, $this->tags] = [
$this->option('provider'), (array) $this->option('tag'),
];

if (! $this->provider && ! $this->tags) {
$this->promptForProviderOrTag();
}
}






protected function promptForProviderOrTag()
{
$choices = $this->publishableChoices();

$choice = windows_os()
? select(
"Which provider or tag's files would you like to publish?",
$choices,
scroll: 15,
)
: search(
label: "Which provider or tag's files would you like to publish?",
placeholder: 'Search...',
options: fn ($search) => array_values(array_filter(
$choices,
fn ($choice) => str_contains(strtolower($choice), strtolower($search))
)),
scroll: 15,
);

if ($choice == $choices[0] || is_null($choice)) {
return;
}

$this->parseChoice($choice);
}






protected function publishableChoices()
{
return array_merge(
['All providers and tags'],
preg_filter('/^/', '<fg=gray>Provider:</> ', Arr::sort(ServiceProvider::publishableProviders())),
preg_filter('/^/', '<fg=gray>Tag:</> ', Arr::sort(ServiceProvider::publishableGroups()))
);
}







protected function parseChoice($choice)
{
[$type, $value] = explode(': ', strip_tags($choice));

if ($type === 'Provider') {
$this->provider = $value;
} elseif ($type === 'Tag') {
$this->tags = [$value];
}
}







protected function publishTag($tag)
{
$pathsToPublish = $this->pathsToPublish($tag);

if ($publishing = count($pathsToPublish) > 0) {
$this->components->info(sprintf(
'Publishing %sassets',
$tag ? "[$tag] " : '',
));
}

foreach ($pathsToPublish as $from => $to) {
$this->publishItem($from, $to);
}

if ($publishing === false) {
$this->components->info('No publishable resources for tag ['.$tag.'].');
} else {
$this->laravel['events']->dispatch(new VendorTagPublished($tag, $pathsToPublish));

$this->newLine();
}
}







protected function pathsToPublish($tag)
{
return ServiceProvider::pathsToPublish(
$this->provider, $tag
);
}








protected function publishItem($from, $to)
{
if ($this->files->isFile($from)) {
return $this->publishFile($from, $to);
} elseif ($this->files->isDirectory($from)) {
return $this->publishDirectory($from, $to);
}

$this->components->error("Can't locate path: <{$from}>");
}








protected function publishFile($from, $to)
{
if ((! $this->option('existing') && (! $this->files->exists($to) || $this->option('force')))
|| ($this->option('existing') && $this->files->exists($to))) {
$to = $this->ensureMigrationNameIsUpToDate($from, $to);

$this->createParentDirectory(dirname($to));

$this->files->copy($from, $to);

$this->status($from, $to, 'file');
} else {
if ($this->option('existing')) {
$this->components->twoColumnDetail(sprintf(
'File [%s] does not exist',
str_replace(base_path().'/', '', $to),
), '<fg=yellow;options=bold>SKIPPED</>');
} else {
$this->components->twoColumnDetail(sprintf(
'File [%s] already exists',
str_replace(base_path().'/', '', realpath($to)),
), '<fg=yellow;options=bold>SKIPPED</>');
}
}
}








protected function publishDirectory($from, $to)
{
$visibility = PortableVisibilityConverter::fromArray([], Visibility::PUBLIC);

$this->moveManagedFiles($from, new MountManager([
'from' => new Flysystem(new LocalAdapter($from)),
'to' => new Flysystem(new LocalAdapter($to, $visibility)),
]));

$this->status($from, $to, 'directory');
}








protected function moveManagedFiles($from, $manager)
{
foreach ($manager->listContents('from://', true)->sortByPath() as $file) {
$path = Str::after($file['path'], 'from://');

if (
$file['type'] === 'file'
&& (
(! $this->option('existing') && (! $manager->fileExists('to://'.$path) || $this->option('force')))
|| ($this->option('existing') && $manager->fileExists('to://'.$path))
)
) {
$path = $this->ensureMigrationNameIsUpToDate($from, $path);

$manager->write('to://'.$path, $manager->read($file['path']));
}
}
}







protected function createParentDirectory($directory)
{
if (! $this->files->isDirectory($directory)) {
$this->files->makeDirectory($directory, 0755, true);
}
}








protected function ensureMigrationNameIsUpToDate($from, $to)
{
if (static::$updateMigrationDates === false) {
return $to;
}

$from = realpath($from);

foreach (ServiceProvider::publishableMigrationPaths() as $path) {
$path = realpath($path);

if ($from === $path && preg_match('/\d{4}_(\d{2})_(\d{2})_(\d{6})_/', $to)) {
$this->publishedAt->addSecond();

return preg_replace(
'/\d{4}_(\d{2})_(\d{2})_(\d{6})_/',
$this->publishedAt->format('Y_m_d_His').'_',
$to,
);
}
}

return $to;
}









protected function status($from, $to, $type)
{
$from = str_replace(base_path().'/', '', realpath($from));

$to = str_replace(base_path().'/', '', realpath($to));

$this->components->task(sprintf(
'Copying %s [%s] to [%s]',
$type,
$from,
$to,
));
}






public static function dontUpdateMigrationDates()
{
static::$updateMigrationDates = false;
}
}
