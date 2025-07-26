<?php





namespace Whoops\Handler;

use Whoops\Inspector\InspectorInterface;
use Whoops\RunInterface;

interface HandlerInterface
{



public function handle();





public function setRun(RunInterface $run);





public function setException($exception);





public function setInspector(InspectorInterface $inspector);
}
