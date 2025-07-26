<?php

declare(strict_types=1);

namespace Pest\Bootstrappers;

use Pest\Contracts\Bootstrapper;
use Pest\Subscribers;
use Pest\Support\Container;
use PHPUnit\Event;
use PHPUnit\Event\Subscriber;




final readonly class BootSubscribers implements Bootstrapper
{





private const SUBSCRIBERS = [
Subscribers\EnsureConfigurationIsAvailable::class,
Subscribers\EnsureIgnorableTestCasesAreIgnored::class,
Subscribers\EnsureKernelDumpIsFlushed::class,
Subscribers\EnsureTeamCityEnabled::class,
];




public function __construct(
private Container $container,
) {}




public function boot(): void
{
foreach (self::SUBSCRIBERS as $subscriber) {
$instance = $this->container->get($subscriber);

assert($instance instanceof Subscriber);

Event\Facade::instance()->registerSubscriber($instance);
}
}
}
