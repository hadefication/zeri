<?php





namespace Whoops\Handler;

use InvalidArgumentException;






class CallbackHandler extends Handler
{



protected $callable;





public function __construct($callable)
{
if (!is_callable($callable)) {
throw new InvalidArgumentException(
'Argument to ' . __METHOD__ . ' must be valid callable'
);
}

$this->callable = $callable;
}




public function handle()
{
$exception = $this->getException();
$inspector = $this->getInspector();
$run = $this->getRun();
$callable = $this->callable;



return $callable($exception, $inspector, $run);
}
}
