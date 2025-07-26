<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers;

use PHPUnit\Event\Application\Finished;
use PHPUnit\Event\Application\FinishedSubscriber;




final class EnsureInitialTestRunWasSuccessful implements FinishedSubscriber
{
public function notify(Finished $event): void {}
}
