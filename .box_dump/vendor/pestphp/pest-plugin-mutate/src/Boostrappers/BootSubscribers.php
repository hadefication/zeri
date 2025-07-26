<?php

declare(strict_types=1);

namespace Pest\Mutate\Boostrappers;

use Pest\Contracts\Bootstrapper;
use Pest\Mutate\Contracts\Subscriber;
use Pest\Mutate\Event\Facade;
use Pest\Mutate\Subscribers\StopOnUncoveredMutation;
use Pest\Mutate\Subscribers\StopOnUntestedMutation;
use Pest\Mutate\Subscribers\TrackMutationSuiteFinish;
use Pest\Mutate\Subscribers\TrackMutationSuiteStart;
use Pest\Support\Container;




final readonly class BootSubscribers implements Bootstrapper
{





private const SUBSCRIBERS = [
TrackMutationSuiteStart::class,
TrackMutationSuiteFinish::class,
StopOnUncoveredMutation::class,
StopOnUntestedMutation::class,
];




public function __construct(
private Container $container,
) {}




public function boot(): void
{
foreach (self::SUBSCRIBERS as $subscriber) {
$instance = $this->container->get($subscriber);

assert($instance instanceof Subscriber);

Facade::instance()->registerSubscriber($instance);
}
}
}
