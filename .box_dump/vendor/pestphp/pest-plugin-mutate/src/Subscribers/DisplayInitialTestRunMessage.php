<?php

declare(strict_types=1);

namespace Pest\Mutate\Subscribers;

use PHPUnit\Event\TestSuite\Loaded;
use PHPUnit\Event\TestSuite\LoadedSubscriber;




final class DisplayInitialTestRunMessage implements LoadedSubscriber
{



public function notify(Loaded $event): void
{

}
}
