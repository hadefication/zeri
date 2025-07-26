<?php

namespace Illuminate\Support\Testing\Fakes;

use Closure;
use Illuminate\Contracts\Mail\Factory;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Mail\MailQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\ReflectsClosures;
use PHPUnit\Framework\Assert as PHPUnit;

class MailFake implements Factory, Fake, Mailer, MailQueue
{
use ForwardsCalls, ReflectsClosures;






public $manager;






protected $currentMailer;






protected $mailables = [];






protected $queuedMailables = [];






public function __construct(MailManager $manager)
{
$this->manager = $manager;
$this->currentMailer = $manager->getDefaultDriver();
}








public function assertSent($mailable, $callback = null)
{
[$mailable, $callback] = $this->prepareMailableAndCallback($mailable, $callback);

if (is_numeric($callback)) {
return $this->assertSentTimes($mailable, $callback);
}

$suggestion = count($this->queuedMailables) ? ' Did you mean to use assertQueued() instead?' : '';

if (is_array($callback) || is_string($callback)) {
foreach (Arr::wrap($callback) as $address) {
$callback = fn ($mail) => $mail->hasTo($address);

PHPUnit::assertTrue(
$this->sent($mailable, $callback)->count() > 0,
"The expected [{$mailable}] mailable was not sent to address [{$address}].".$suggestion
);
}

return;
}

PHPUnit::assertTrue(
$this->sent($mailable, $callback)->count() > 0,
"The expected [{$mailable}] mailable was not sent.".$suggestion
);
}








protected function assertSentTimes($mailable, $times = 1)
{
$count = $this->sent($mailable)->count();

PHPUnit::assertSame(
$times, $count,
"The expected [{$mailable}] mailable was sent {$count} times instead of {$times} times."
);
}








public function assertNotOutgoing($mailable, $callback = null)
{
$this->assertNotSent($mailable, $callback);
$this->assertNotQueued($mailable, $callback);
}








public function assertNotSent($mailable, $callback = null)
{
if (is_string($callback) || is_array($callback)) {
foreach (Arr::wrap($callback) as $address) {
$callback = fn ($mail) => $mail->hasTo($address);

PHPUnit::assertCount(
0, $this->sent($mailable, $callback),
"The unexpected [{$mailable}] mailable was sent to address [{$address}]."
);
}

return;
}

[$mailable, $callback] = $this->prepareMailableAndCallback($mailable, $callback);

PHPUnit::assertCount(
0, $this->sent($mailable, $callback),
"The unexpected [{$mailable}] mailable was sent."
);
}






public function assertNothingOutgoing()
{
$this->assertNothingSent();
$this->assertNothingQueued();
}






public function assertNothingSent()
{
$mailableNames = (new Collection($this->mailables))->map(
fn ($mailable) => get_class($mailable)
)->join("\n- ");

PHPUnit::assertEmpty($this->mailables, "The following mailables were sent unexpectedly:\n\n- $mailableNames\n");
}








public function assertQueued($mailable, $callback = null)
{
[$mailable, $callback] = $this->prepareMailableAndCallback($mailable, $callback);

if (is_numeric($callback)) {
return $this->assertQueuedTimes($mailable, $callback);
}

if (is_string($callback) || is_array($callback)) {
foreach (Arr::wrap($callback) as $address) {
$callback = fn ($mail) => $mail->hasTo($address);

PHPUnit::assertTrue(
$this->queued($mailable, $callback)->count() > 0,
"The expected [{$mailable}] mailable was not queued to address [{$address}]."
);
}

return;
}

PHPUnit::assertTrue(
$this->queued($mailable, $callback)->count() > 0,
"The expected [{$mailable}] mailable was not queued."
);
}








protected function assertQueuedTimes($mailable, $times = 1)
{
$count = $this->queued($mailable)->count();

PHPUnit::assertSame(
$times, $count,
"The expected [{$mailable}] mailable was queued {$count} times instead of {$times} times."
);
}








public function assertNotQueued($mailable, $callback = null)
{
if (is_string($callback) || is_array($callback)) {
foreach (Arr::wrap($callback) as $address) {
$callback = fn ($mail) => $mail->hasTo($address);

PHPUnit::assertCount(
0, $this->queued($mailable, $callback),
"The unexpected [{$mailable}] mailable was queued to address [{$address}]."
);
}

return;
}

[$mailable, $callback] = $this->prepareMailableAndCallback($mailable, $callback);

PHPUnit::assertCount(
0, $this->queued($mailable, $callback),
"The unexpected [{$mailable}] mailable was queued."
);
}






public function assertNothingQueued()
{
$mailableNames = (new Collection($this->queuedMailables))->map(
fn ($mailable) => get_class($mailable)
)->join("\n- ");

PHPUnit::assertEmpty($this->queuedMailables, "The following mailables were queued unexpectedly:\n\n- $mailableNames\n");
}







public function assertSentCount($count)
{
$total = (new Collection($this->mailables))->count();

PHPUnit::assertSame(
$count, $total,
"The total number of mailables sent was {$total} instead of {$count}."
);
}







public function assertQueuedCount($count)
{
$total = (new Collection($this->queuedMailables))->count();

PHPUnit::assertSame(
$count, $total,
"The total number of mailables queued was {$total} instead of {$count}."
);
}







public function assertOutgoingCount($count)
{
$total = (new Collection($this->mailables))
->concat($this->queuedMailables)
->count();

PHPUnit::assertSame(
$count, $total,
"The total number of outgoing mailables was {$total} instead of {$count}."
);
}








public function sent($mailable, $callback = null)
{
[$mailable, $callback] = $this->prepareMailableAndCallback($mailable, $callback);

if (! $this->hasSent($mailable)) {
return new Collection;
}

$callback = $callback ?: fn () => true;

return $this->mailablesOf($mailable)->filter(fn ($mailable) => $callback($mailable));
}







public function hasSent($mailable)
{
return $this->mailablesOf($mailable)->count() > 0;
}








public function queued($mailable, $callback = null)
{
[$mailable, $callback] = $this->prepareMailableAndCallback($mailable, $callback);

if (! $this->hasQueued($mailable)) {
return new Collection;
}

$callback = $callback ?: fn () => true;

return $this->queuedMailablesOf($mailable)->filter(fn ($mailable) => $callback($mailable));
}







public function hasQueued($mailable)
{
return $this->queuedMailablesOf($mailable)->count() > 0;
}







protected function mailablesOf($type)
{
return (new Collection($this->mailables))->filter(fn ($mailable) => $mailable instanceof $type);
}







protected function queuedMailablesOf($type)
{
return (new Collection($this->queuedMailables))->filter(fn ($mailable) => $mailable instanceof $type);
}







public function mailer($name = null)
{
$this->currentMailer = $name;

return $this;
}







public function to($users)
{
return (new PendingMailFake($this))->to($users);
}







public function cc($users)
{
return (new PendingMailFake($this))->cc($users);
}







public function bcc($users)
{
return (new PendingMailFake($this))->bcc($users);
}








public function raw($text, $callback)
{

}









public function send($view, array $data = [], $callback = null)
{
return $this->sendMail($view, $view instanceof ShouldQueue);
}









public function sendNow($mailable, array $data = [], $callback = null)
{
$this->sendMail($mailable, shouldQueue: false);
}








protected function sendMail($view, $shouldQueue = false)
{
if (! $view instanceof Mailable) {
return;
}

$view->mailer($this->currentMailer);

if ($shouldQueue) {
return $this->queue($view);
}

$this->currentMailer = null;

$this->mailables[] = $view;
}








public function queue($view, $queue = null)
{
if (! $view instanceof Mailable) {
return;
}

$view->mailer($this->currentMailer);

$this->currentMailer = null;

$this->queuedMailables[] = $view;
}









public function later($delay, $view, $queue = null)
{
$this->queue($view, $queue);
}








protected function prepareMailableAndCallback($mailable, $callback)
{
if ($mailable instanceof Closure) {
return [$this->firstClosureParameterType($mailable), $mailable];
}

return [$mailable, $callback];
}






public function forgetMailers()
{
$this->currentMailer = null;

return $this;
}








public function __call($method, $parameters)
{
return $this->forwardCallTo($this->manager, $method, $parameters);
}
}
