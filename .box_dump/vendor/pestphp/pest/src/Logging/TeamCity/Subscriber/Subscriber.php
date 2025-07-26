<?php

declare(strict_types=1);

namespace Pest\Logging\TeamCity\Subscriber;

use Pest\Logging\TeamCity\TeamCityLogger;




abstract class Subscriber 
{



public function __construct(private readonly TeamCityLogger $logger) {}




final protected function logger(): TeamCityLogger 
{
return $this->logger;
}
}
