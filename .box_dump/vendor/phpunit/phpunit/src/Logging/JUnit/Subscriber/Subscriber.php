<?php declare(strict_types=1);








namespace PHPUnit\Logging\JUnit;

/**
@no-named-arguments


*/
abstract readonly class Subscriber
{
private JunitXmlLogger $logger;

public function __construct(JunitXmlLogger $logger)
{
$this->logger = $logger;
}

protected function logger(): JunitXmlLogger
{
return $this->logger;
}
}
