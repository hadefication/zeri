<?php

namespace Illuminate\Foundation\Providers;

use Illuminate\Auth\Console\ClearResetsCommand;
use Illuminate\Cache\Console\CacheTableCommand;
use Illuminate\Cache\Console\ClearCommand as CacheClearCommand;
use Illuminate\Cache\Console\ForgetCommand as CacheForgetCommand;
use Illuminate\Cache\Console\PruneStaleTagsCommand;
use Illuminate\Concurrency\Console\InvokeSerializedClosureCommand;
use Illuminate\Console\Scheduling\ScheduleClearCacheCommand;
use Illuminate\Console\Scheduling\ScheduleFinishCommand;
use Illuminate\Console\Scheduling\ScheduleInterruptCommand;
use Illuminate\Console\Scheduling\ScheduleListCommand;
use Illuminate\Console\Scheduling\ScheduleRunCommand;
use Illuminate\Console\Scheduling\ScheduleTestCommand;
use Illuminate\Console\Scheduling\ScheduleWorkCommand;
use Illuminate\Console\Signals;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Console\DbCommand;
use Illuminate\Database\Console\DumpCommand;
use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Database\Console\MonitorCommand as DatabaseMonitorCommand;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Database\Console\ShowCommand;
use Illuminate\Database\Console\ShowModelCommand;
use Illuminate\Database\Console\TableCommand as DatabaseTableCommand;
use Illuminate\Database\Console\WipeCommand;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Foundation\Console\ApiInstallCommand;
use Illuminate\Foundation\Console\BroadcastingInstallCommand;
use Illuminate\Foundation\Console\CastMakeCommand;
use Illuminate\Foundation\Console\ChannelListCommand;
use Illuminate\Foundation\Console\ChannelMakeCommand;
use Illuminate\Foundation\Console\ClassMakeCommand;
use Illuminate\Foundation\Console\ClearCompiledCommand;
use Illuminate\Foundation\Console\ComponentMakeCommand;
use Illuminate\Foundation\Console\ConfigCacheCommand;
use Illuminate\Foundation\Console\ConfigClearCommand;
use Illuminate\Foundation\Console\ConfigPublishCommand;
use Illuminate\Foundation\Console\ConfigShowCommand;
use Illuminate\Foundation\Console\ConsoleMakeCommand;
use Illuminate\Foundation\Console\DocsCommand;
use Illuminate\Foundation\Console\DownCommand;
use Illuminate\Foundation\Console\EnumMakeCommand;
use Illuminate\Foundation\Console\EnvironmentCommand;
use Illuminate\Foundation\Console\EnvironmentDecryptCommand;
use Illuminate\Foundation\Console\EnvironmentEncryptCommand;
use Illuminate\Foundation\Console\EventCacheCommand;
use Illuminate\Foundation\Console\EventClearCommand;
use Illuminate\Foundation\Console\EventGenerateCommand;
use Illuminate\Foundation\Console\EventListCommand;
use Illuminate\Foundation\Console\EventMakeCommand;
use Illuminate\Foundation\Console\ExceptionMakeCommand;
use Illuminate\Foundation\Console\InterfaceMakeCommand;
use Illuminate\Foundation\Console\JobMakeCommand;
use Illuminate\Foundation\Console\JobMiddlewareMakeCommand;
use Illuminate\Foundation\Console\KeyGenerateCommand;
use Illuminate\Foundation\Console\LangPublishCommand;
use Illuminate\Foundation\Console\ListenerMakeCommand;
use Illuminate\Foundation\Console\MailMakeCommand;
use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Foundation\Console\NotificationMakeCommand;
use Illuminate\Foundation\Console\ObserverMakeCommand;
use Illuminate\Foundation\Console\OptimizeClearCommand;
use Illuminate\Foundation\Console\OptimizeCommand;
use Illuminate\Foundation\Console\PackageDiscoverCommand;
use Illuminate\Foundation\Console\PolicyMakeCommand;
use Illuminate\Foundation\Console\ProviderMakeCommand;
use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Foundation\Console\ResourceMakeCommand;
use Illuminate\Foundation\Console\RouteCacheCommand;
use Illuminate\Foundation\Console\RouteClearCommand;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Foundation\Console\RuleMakeCommand;
use Illuminate\Foundation\Console\ScopeMakeCommand;
use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Foundation\Console\StorageLinkCommand;
use Illuminate\Foundation\Console\StorageUnlinkCommand;
use Illuminate\Foundation\Console\StubPublishCommand;
use Illuminate\Foundation\Console\TestMakeCommand;
use Illuminate\Foundation\Console\TraitMakeCommand;
use Illuminate\Foundation\Console\UpCommand;
use Illuminate\Foundation\Console\VendorPublishCommand;
use Illuminate\Foundation\Console\ViewCacheCommand;
use Illuminate\Foundation\Console\ViewClearCommand;
use Illuminate\Foundation\Console\ViewMakeCommand;
use Illuminate\Notifications\Console\NotificationTableCommand;
use Illuminate\Queue\Console\BatchesTableCommand;
use Illuminate\Queue\Console\ClearCommand as QueueClearCommand;
use Illuminate\Queue\Console\FailedTableCommand;
use Illuminate\Queue\Console\FlushFailedCommand as FlushFailedQueueCommand;
use Illuminate\Queue\Console\ForgetFailedCommand as ForgetFailedQueueCommand;
use Illuminate\Queue\Console\ListenCommand as QueueListenCommand;
use Illuminate\Queue\Console\ListFailedCommand as ListFailedQueueCommand;
use Illuminate\Queue\Console\MonitorCommand as QueueMonitorCommand;
use Illuminate\Queue\Console\PruneBatchesCommand as QueuePruneBatchesCommand;
use Illuminate\Queue\Console\PruneFailedJobsCommand as QueuePruneFailedJobsCommand;
use Illuminate\Queue\Console\RestartCommand as QueueRestartCommand;
use Illuminate\Queue\Console\RetryBatchCommand as QueueRetryBatchCommand;
use Illuminate\Queue\Console\RetryCommand as QueueRetryCommand;
use Illuminate\Queue\Console\TableCommand;
use Illuminate\Queue\Console\WorkCommand as QueueWorkCommand;
use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Routing\Console\MiddlewareMakeCommand;
use Illuminate\Session\Console\SessionTableCommand;
use Illuminate\Support\ServiceProvider;

class ArtisanServiceProvider extends ServiceProvider implements DeferrableProvider
{





protected $commands = [
'About' => AboutCommand::class,
'CacheClear' => CacheClearCommand::class,
'CacheForget' => CacheForgetCommand::class,
'ClearCompiled' => ClearCompiledCommand::class,
'ClearResets' => ClearResetsCommand::class,
'ConfigCache' => ConfigCacheCommand::class,
'ConfigClear' => ConfigClearCommand::class,
'ConfigShow' => ConfigShowCommand::class,
'Db' => DbCommand::class,
'DbMonitor' => DatabaseMonitorCommand::class,
'DbPrune' => PruneCommand::class,
'DbShow' => ShowCommand::class,
'DbTable' => DatabaseTableCommand::class,
'DbWipe' => WipeCommand::class,
'Down' => DownCommand::class,
'Environment' => EnvironmentCommand::class,
'EnvironmentDecrypt' => EnvironmentDecryptCommand::class,
'EnvironmentEncrypt' => EnvironmentEncryptCommand::class,
'EventCache' => EventCacheCommand::class,
'EventClear' => EventClearCommand::class,
'EventList' => EventListCommand::class,
'InvokeSerializedClosure' => InvokeSerializedClosureCommand::class,
'KeyGenerate' => KeyGenerateCommand::class,
'Optimize' => OptimizeCommand::class,
'OptimizeClear' => OptimizeClearCommand::class,
'PackageDiscover' => PackageDiscoverCommand::class,
'PruneStaleTagsCommand' => PruneStaleTagsCommand::class,
'QueueClear' => QueueClearCommand::class,
'QueueFailed' => ListFailedQueueCommand::class,
'QueueFlush' => FlushFailedQueueCommand::class,
'QueueForget' => ForgetFailedQueueCommand::class,
'QueueListen' => QueueListenCommand::class,
'QueueMonitor' => QueueMonitorCommand::class,
'QueuePruneBatches' => QueuePruneBatchesCommand::class,
'QueuePruneFailedJobs' => QueuePruneFailedJobsCommand::class,
'QueueRestart' => QueueRestartCommand::class,
'QueueRetry' => QueueRetryCommand::class,
'QueueRetryBatch' => QueueRetryBatchCommand::class,
'QueueWork' => QueueWorkCommand::class,
'RouteCache' => RouteCacheCommand::class,
'RouteClear' => RouteClearCommand::class,
'RouteList' => RouteListCommand::class,
'SchemaDump' => DumpCommand::class,
'Seed' => SeedCommand::class,
'ScheduleFinish' => ScheduleFinishCommand::class,
'ScheduleList' => ScheduleListCommand::class,
'ScheduleRun' => ScheduleRunCommand::class,
'ScheduleClearCache' => ScheduleClearCacheCommand::class,
'ScheduleTest' => ScheduleTestCommand::class,
'ScheduleWork' => ScheduleWorkCommand::class,
'ScheduleInterrupt' => ScheduleInterruptCommand::class,
'ShowModel' => ShowModelCommand::class,
'StorageLink' => StorageLinkCommand::class,
'StorageUnlink' => StorageUnlinkCommand::class,
'Up' => UpCommand::class,
'ViewCache' => ViewCacheCommand::class,
'ViewClear' => ViewClearCommand::class,
];






protected $devCommands = [
'ApiInstall' => ApiInstallCommand::class,
'BroadcastingInstall' => BroadcastingInstallCommand::class,
'CacheTable' => CacheTableCommand::class,
'CastMake' => CastMakeCommand::class,
'ChannelList' => ChannelListCommand::class,
'ChannelMake' => ChannelMakeCommand::class,
'ClassMake' => ClassMakeCommand::class,
'ComponentMake' => ComponentMakeCommand::class,
'ConfigPublish' => ConfigPublishCommand::class,
'ConsoleMake' => ConsoleMakeCommand::class,
'ControllerMake' => ControllerMakeCommand::class,
'Docs' => DocsCommand::class,
'EnumMake' => EnumMakeCommand::class,
'EventGenerate' => EventGenerateCommand::class,
'EventMake' => EventMakeCommand::class,
'ExceptionMake' => ExceptionMakeCommand::class,
'FactoryMake' => FactoryMakeCommand::class,
'InterfaceMake' => InterfaceMakeCommand::class,
'JobMake' => JobMakeCommand::class,
'JobMiddlewareMake' => JobMiddlewareMakeCommand::class,
'LangPublish' => LangPublishCommand::class,
'ListenerMake' => ListenerMakeCommand::class,
'MailMake' => MailMakeCommand::class,
'MiddlewareMake' => MiddlewareMakeCommand::class,
'ModelMake' => ModelMakeCommand::class,
'NotificationMake' => NotificationMakeCommand::class,
'NotificationTable' => NotificationTableCommand::class,
'ObserverMake' => ObserverMakeCommand::class,
'PolicyMake' => PolicyMakeCommand::class,
'ProviderMake' => ProviderMakeCommand::class,
'QueueFailedTable' => FailedTableCommand::class,
'QueueTable' => TableCommand::class,
'QueueBatchesTable' => BatchesTableCommand::class,
'RequestMake' => RequestMakeCommand::class,
'ResourceMake' => ResourceMakeCommand::class,
'RuleMake' => RuleMakeCommand::class,
'ScopeMake' => ScopeMakeCommand::class,
'SeederMake' => SeederMakeCommand::class,
'SessionTable' => SessionTableCommand::class,
'Serve' => ServeCommand::class,
'StubPublish' => StubPublishCommand::class,
'TestMake' => TestMakeCommand::class,
'TraitMake' => TraitMakeCommand::class,
'VendorPublish' => VendorPublishCommand::class,
'ViewMake' => ViewMakeCommand::class,
];






public function register()
{
$this->registerCommands(array_merge(
$this->commands,
$this->devCommands
));

Signals::resolveAvailabilityUsing(function () {
return $this->app->runningInConsole()
&& ! $this->app->runningUnitTests()
&& extension_loaded('pcntl');
});
}







protected function registerCommands(array $commands)
{
foreach ($commands as $commandName => $command) {
$method = "register{$commandName}Command";

if (method_exists($this, $method)) {
$this->{$method}();
} else {
$this->app->singleton($command);
}
}

$this->commands(array_values($commands));
}






protected function registerAboutCommand()
{
$this->app->singleton(AboutCommand::class, function ($app) {
return new AboutCommand($app['composer']);
});
}






protected function registerCacheClearCommand()
{
$this->app->singleton(CacheClearCommand::class, function ($app) {
return new CacheClearCommand($app['cache'], $app['files']);
});
}






protected function registerCacheForgetCommand()
{
$this->app->singleton(CacheForgetCommand::class, function ($app) {
return new CacheForgetCommand($app['cache']);
});
}






protected function registerCacheTableCommand()
{
$this->app->singleton(CacheTableCommand::class, function ($app) {
return new CacheTableCommand($app['files']);
});
}






protected function registerCastMakeCommand()
{
$this->app->singleton(CastMakeCommand::class, function ($app) {
return new CastMakeCommand($app['files']);
});
}






protected function registerChannelMakeCommand()
{
$this->app->singleton(ChannelMakeCommand::class, function ($app) {
return new ChannelMakeCommand($app['files']);
});
}






protected function registerClassMakeCommand()
{
$this->app->singleton(ClassMakeCommand::class, function ($app) {
return new ClassMakeCommand($app['files']);
});
}






protected function registerComponentMakeCommand()
{
$this->app->singleton(ComponentMakeCommand::class, function ($app) {
return new ComponentMakeCommand($app['files']);
});
}






protected function registerConfigCacheCommand()
{
$this->app->singleton(ConfigCacheCommand::class, function ($app) {
return new ConfigCacheCommand($app['files']);
});
}






protected function registerConfigClearCommand()
{
$this->app->singleton(ConfigClearCommand::class, function ($app) {
return new ConfigClearCommand($app['files']);
});
}






protected function registerConfigPublishCommand()
{
$this->app->singleton(ConfigPublishCommand::class, function ($app) {
return new ConfigPublishCommand;
});
}






protected function registerConsoleMakeCommand()
{
$this->app->singleton(ConsoleMakeCommand::class, function ($app) {
return new ConsoleMakeCommand($app['files']);
});
}






protected function registerControllerMakeCommand()
{
$this->app->singleton(ControllerMakeCommand::class, function ($app) {
return new ControllerMakeCommand($app['files']);
});
}






protected function registerEnumMakeCommand()
{
$this->app->singleton(EnumMakeCommand::class, function ($app) {
return new EnumMakeCommand($app['files']);
});
}






protected function registerEventMakeCommand()
{
$this->app->singleton(EventMakeCommand::class, function ($app) {
return new EventMakeCommand($app['files']);
});
}






protected function registerExceptionMakeCommand()
{
$this->app->singleton(ExceptionMakeCommand::class, function ($app) {
return new ExceptionMakeCommand($app['files']);
});
}






protected function registerFactoryMakeCommand()
{
$this->app->singleton(FactoryMakeCommand::class, function ($app) {
return new FactoryMakeCommand($app['files']);
});
}






protected function registerEventClearCommand()
{
$this->app->singleton(EventClearCommand::class, function ($app) {
return new EventClearCommand($app['files']);
});
}






protected function registerInterfaceMakeCommand()
{
$this->app->singleton(InterfaceMakeCommand::class, function ($app) {
return new InterfaceMakeCommand($app['files']);
});
}






protected function registerJobMakeCommand()
{
$this->app->singleton(JobMakeCommand::class, function ($app) {
return new JobMakeCommand($app['files']);
});
}






protected function registerJobMiddlewareMakeCommand()
{
$this->app->singleton(JobMiddlewareMakeCommand::class, function ($app) {
return new JobMiddlewareMakeCommand($app['files']);
});
}






protected function registerListenerMakeCommand()
{
$this->app->singleton(ListenerMakeCommand::class, function ($app) {
return new ListenerMakeCommand($app['files']);
});
}






protected function registerMailMakeCommand()
{
$this->app->singleton(MailMakeCommand::class, function ($app) {
return new MailMakeCommand($app['files']);
});
}






protected function registerMiddlewareMakeCommand()
{
$this->app->singleton(MiddlewareMakeCommand::class, function ($app) {
return new MiddlewareMakeCommand($app['files']);
});
}






protected function registerModelMakeCommand()
{
$this->app->singleton(ModelMakeCommand::class, function ($app) {
return new ModelMakeCommand($app['files']);
});
}






protected function registerNotificationMakeCommand()
{
$this->app->singleton(NotificationMakeCommand::class, function ($app) {
return new NotificationMakeCommand($app['files']);
});
}






protected function registerNotificationTableCommand()
{
$this->app->singleton(NotificationTableCommand::class, function ($app) {
return new NotificationTableCommand($app['files']);
});
}






protected function registerObserverMakeCommand()
{
$this->app->singleton(ObserverMakeCommand::class, function ($app) {
return new ObserverMakeCommand($app['files']);
});
}






protected function registerPolicyMakeCommand()
{
$this->app->singleton(PolicyMakeCommand::class, function ($app) {
return new PolicyMakeCommand($app['files']);
});
}






protected function registerProviderMakeCommand()
{
$this->app->singleton(ProviderMakeCommand::class, function ($app) {
return new ProviderMakeCommand($app['files']);
});
}






protected function registerQueueForgetCommand()
{
$this->app->singleton(ForgetFailedQueueCommand::class);
}






protected function registerQueueListenCommand()
{
$this->app->singleton(QueueListenCommand::class, function ($app) {
return new QueueListenCommand($app['queue.listener']);
});
}






protected function registerQueueMonitorCommand()
{
$this->app->singleton(QueueMonitorCommand::class, function ($app) {
return new QueueMonitorCommand($app['queue'], $app['events']);
});
}






protected function registerQueuePruneBatchesCommand()
{
$this->app->singleton(QueuePruneBatchesCommand::class, function () {
return new QueuePruneBatchesCommand;
});
}






protected function registerQueuePruneFailedJobsCommand()
{
$this->app->singleton(QueuePruneFailedJobsCommand::class, function () {
return new QueuePruneFailedJobsCommand;
});
}






protected function registerQueueRestartCommand()
{
$this->app->singleton(QueueRestartCommand::class, function ($app) {
return new QueueRestartCommand($app['cache.store']);
});
}






protected function registerQueueWorkCommand()
{
$this->app->singleton(QueueWorkCommand::class, function ($app) {
return new QueueWorkCommand($app['queue.worker'], $app['cache.store']);
});
}






protected function registerQueueFailedTableCommand()
{
$this->app->singleton(FailedTableCommand::class, function ($app) {
return new FailedTableCommand($app['files']);
});
}






protected function registerQueueTableCommand()
{
$this->app->singleton(TableCommand::class, function ($app) {
return new TableCommand($app['files']);
});
}






protected function registerQueueBatchesTableCommand()
{
$this->app->singleton(BatchesTableCommand::class, function ($app) {
return new BatchesTableCommand($app['files']);
});
}






protected function registerRequestMakeCommand()
{
$this->app->singleton(RequestMakeCommand::class, function ($app) {
return new RequestMakeCommand($app['files']);
});
}






protected function registerResourceMakeCommand()
{
$this->app->singleton(ResourceMakeCommand::class, function ($app) {
return new ResourceMakeCommand($app['files']);
});
}






protected function registerRuleMakeCommand()
{
$this->app->singleton(RuleMakeCommand::class, function ($app) {
return new RuleMakeCommand($app['files']);
});
}






protected function registerScopeMakeCommand()
{
$this->app->singleton(ScopeMakeCommand::class, function ($app) {
return new ScopeMakeCommand($app['files']);
});
}






protected function registerSeederMakeCommand()
{
$this->app->singleton(SeederMakeCommand::class, function ($app) {
return new SeederMakeCommand($app['files']);
});
}






protected function registerSessionTableCommand()
{
$this->app->singleton(SessionTableCommand::class, function ($app) {
return new SessionTableCommand($app['files']);
});
}






protected function registerRouteCacheCommand()
{
$this->app->singleton(RouteCacheCommand::class, function ($app) {
return new RouteCacheCommand($app['files']);
});
}






protected function registerRouteClearCommand()
{
$this->app->singleton(RouteClearCommand::class, function ($app) {
return new RouteClearCommand($app['files']);
});
}






protected function registerRouteListCommand()
{
$this->app->singleton(RouteListCommand::class, function ($app) {
return new RouteListCommand($app['router']);
});
}






protected function registerSeedCommand()
{
$this->app->singleton(SeedCommand::class, function ($app) {
return new SeedCommand($app['db']);
});
}






protected function registerTestMakeCommand()
{
$this->app->singleton(TestMakeCommand::class, function ($app) {
return new TestMakeCommand($app['files']);
});
}






protected function registerTraitMakeCommand()
{
$this->app->singleton(TraitMakeCommand::class, function ($app) {
return new TraitMakeCommand($app['files']);
});
}






protected function registerVendorPublishCommand()
{
$this->app->singleton(VendorPublishCommand::class, function ($app) {
return new VendorPublishCommand($app['files']);
});
}






protected function registerViewClearCommand()
{
$this->app->singleton(ViewClearCommand::class, function ($app) {
return new ViewClearCommand($app['files']);
});
}






public function provides()
{
return array_merge(array_values($this->commands), array_values($this->devCommands));
}
}
