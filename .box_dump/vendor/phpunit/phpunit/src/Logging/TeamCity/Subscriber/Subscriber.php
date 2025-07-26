<?php declare(strict_types=1);








namespace PHPUnit\Logging\TeamCity;

/**
@no-named-arguments


*/
abstract readonly class Subscriber
{
private TeamCityLogger $logger;

public function __construct(TeamCityLogger $logger)
{
$this->logger = $logger;
}

protected function logger(): TeamCityLogger
{
return $this->logger;
}
}
