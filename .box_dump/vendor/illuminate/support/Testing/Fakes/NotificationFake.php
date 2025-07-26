<?php

namespace Illuminate\Support\Testing\Fakes;

use Closure;
use Exception;
use Illuminate\Contracts\Notifications\Dispatcher as NotificationDispatcher;
use Illuminate\Contracts\Notifications\Factory as NotificationFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\ReflectsClosures;
use PHPUnit\Framework\Assert as PHPUnit;

class NotificationFake implements Fake, NotificationDispatcher, NotificationFactory
{
use Macroable, ReflectsClosures;






protected $notifications = [];






public $locale;






protected $serializeAndRestore = false;










public function assertSentOnDemand($notification, $callback = null)
{
$this->assertSentTo(new AnonymousNotifiable, $notification, $callback);
}











public function assertSentTo($notifiable, $notification, $callback = null)
{
if (is_array($notifiable) || $notifiable instanceof Collection) {
if (count($notifiable) === 0) {
throw new Exception('No notifiable given.');
}

foreach ($notifiable as $singleNotifiable) {
$this->assertSentTo($singleNotifiable, $notification, $callback);
}

return;
}

if ($notification instanceof Closure) {
[$notification, $callback] = [$this->firstClosureParameterType($notification), $notification];
}

if (is_numeric($callback)) {
return $this->assertSentToTimes($notifiable, $notification, $callback);
}

PHPUnit::assertTrue(
$this->sent($notifiable, $notification, $callback)->count() > 0,
"The expected [{$notification}] notification was not sent."
);
}








public function assertSentOnDemandTimes($notification, $times = 1)
{
$this->assertSentToTimes(new AnonymousNotifiable, $notification, $times);
}









public function assertSentToTimes($notifiable, $notification, $times = 1)
{
$count = $this->sent($notifiable, $notification)->count();

PHPUnit::assertSame(
$times, $count,
"Expected [{$notification}] to be sent {$times} times, but was sent {$count} times."
);
}











public function assertNotSentTo($notifiable, $notification, $callback = null)
{
if (is_array($notifiable) || $notifiable instanceof Collection) {
if (count($notifiable) === 0) {
throw new Exception('No notifiable given.');
}

foreach ($notifiable as $singleNotifiable) {
$this->assertNotSentTo($singleNotifiable, $notification, $callback);
}

return;
}

if ($notification instanceof Closure) {
[$notification, $callback] = [$this->firstClosureParameterType($notification), $notification];
}

PHPUnit::assertCount(
0, $this->sent($notifiable, $notification, $callback),
"The unexpected [{$notification}] notification was sent."
);
}






public function assertNothingSent()
{
$notificationNames = (new Collection($this->notifications))
->map(fn ($notifiableModels) => (new Collection($notifiableModels))
->map(fn ($notifiables) => (new Collection($notifiables))->keys())
)
->flatten()->join("\n- ");

PHPUnit::assertEmpty($this->notifications, "The following notifications were sent unexpectedly:\n\n- $notificationNames\n");
}









public function assertNothingSentTo($notifiable)
{
if (is_array($notifiable) || $notifiable instanceof Collection) {
if (count($notifiable) === 0) {
throw new Exception('No notifiable given.');
}

foreach ($notifiable as $singleNotifiable) {
$this->assertNothingSentTo($singleNotifiable);
}

return;
}

PHPUnit::assertEmpty(
$this->notifications[get_class($notifiable)][$notifiable->getKey()] ?? [],
'Notifications were sent unexpectedly.',
);
}








public function assertSentTimes($notification, $expectedCount)
{
$actualCount = (new Collection($this->notifications))
->flatten(1)
->reduce(fn ($count, $sent) => $count + count($sent[$notification] ?? []), 0);

PHPUnit::assertSame(
$expectedCount, $actualCount,
"Expected [{$notification}] to be sent {$expectedCount} times, but was sent {$actualCount} times."
);
}







public function assertCount($expectedCount)
{
$actualCount = (new Collection($this->notifications))->flatten(3)->count();

PHPUnit::assertSame(
$expectedCount, $actualCount,
"Expected {$expectedCount} notifications to be sent, but {$actualCount} were sent."
);
}









public function sent($notifiable, $notification, $callback = null)
{
if (! $this->hasSent($notifiable, $notification)) {
return new Collection;
}

$callback = $callback ?: fn () => true;

$notifications = new Collection($this->notificationsFor($notifiable, $notification));

return $notifications->filter(
fn ($arguments) => $callback(...array_values($arguments))
)->pluck('notification');
}








public function hasSent($notifiable, $notification)
{
return ! empty($this->notificationsFor($notifiable, $notification));
}








protected function notificationsFor($notifiable, $notification)
{
return $this->notifications[get_class($notifiable)][(string) $notifiable->getKey()][$notification] ?? [];
}








public function send($notifiables, $notification)
{
$this->sendNow($notifiables, $notification);
}









public function sendNow($notifiables, $notification, ?array $channels = null)
{
if (! $notifiables instanceof Collection && ! is_array($notifiables)) {
$notifiables = [$notifiables];
}

foreach ($notifiables as $notifiable) {
if (! $notification->id) {
$notification->id = Str::uuid()->toString();
}

$notifiableChannels = $channels ?: $notification->via($notifiable);

if (method_exists($notification, 'shouldSend')) {
$notifiableChannels = array_filter(
$notifiableChannels,
fn ($channel) => $notification->shouldSend($notifiable, $channel) !== false
);
}

if (empty($notifiableChannels)) {
continue;
}

$this->notifications[get_class($notifiable)][(string) $notifiable->getKey()][get_class($notification)][] = [
'notification' => $this->serializeAndRestore && $notification instanceof ShouldQueue
? $this->serializeAndRestoreNotification($notification)
: $notification,
'channels' => $notifiableChannels,
'notifiable' => $notifiable,
'locale' => $notification->locale ?? $this->locale ?? value(function () use ($notifiable) {
if ($notifiable instanceof HasLocalePreference) {
return $notifiable->preferredLocale();
}
}),
];
}
}







public function channel($name = null)
{

}







public function locale($locale)
{
$this->locale = $locale;

return $this;
}







public function serializeAndRestore(bool $serializeAndRestore = true)
{
$this->serializeAndRestore = $serializeAndRestore;

return $this;
}







protected function serializeAndRestoreNotification($notification)
{
return unserialize(serialize($notification));
}






public function sentNotifications()
{
return $this->notifications;
}
}
