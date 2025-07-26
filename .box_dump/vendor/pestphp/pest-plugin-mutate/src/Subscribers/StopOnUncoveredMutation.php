<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers;

use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Event\Events\Test\Outcome\Uncovered;
use Pest\Mutate\Event\Events\Test\Outcome\UncoveredSubscriber;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Support\Container;




final class StopOnUncoveredMutation implements UncoveredSubscriber
{
public function notify(Uncovered $event): void
{
if (! Container::getInstance()->get(ConfigurationRepository::class) 
->mergedConfiguration()
->stopOnUncovered) {
return;
}

Container::getInstance()->get(MutationTestRunner::class) 
->stopExecution();
}
}
