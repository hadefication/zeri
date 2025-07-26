<?php









namespace Mockery;

class ExpectsHigherOrderMessage extends HigherOrderMessage
{
public function __construct(MockInterface $mock)
{
parent::__construct($mock, 'shouldReceive');
}







public function __call($method, $args)
{
$expectation = parent::__call($method, $args);

return $expectation->once();
}
}
