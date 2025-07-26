<?php









namespace Mockery\Exception;

class BadMethodCallException extends \BadMethodCallException implements MockeryExceptionInterface
{



private $dismissed = false;

public function dismiss()
{
$this->dismissed = true;

$previous = $this->getPrevious();
if (! $previous instanceof self) {
return;
}

$previous->dismiss();
}




public function dismissed()
{
return $this->dismissed;
}
}
