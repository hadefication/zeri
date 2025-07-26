<?php





namespace Whoops\Handler;

use Whoops\Inspector\InspectorInterface;
use Whoops\RunInterface;




abstract class Handler implements HandlerInterface
{




const DONE = 0x10; 





const LAST_HANDLER = 0x20;



const QUIT = 0x30;




private $run;




private $inspector;




private $exception;




public function setRun(RunInterface $run)
{
$this->run = $run;
}




protected function getRun()
{
return $this->run;
}




public function setInspector(InspectorInterface $inspector)
{
$this->inspector = $inspector;
}




protected function getInspector()
{
return $this->inspector;
}




public function setException($exception)
{
$this->exception = $exception;
}




protected function getException()
{
return $this->exception;
}
}
