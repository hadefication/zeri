<?php





namespace Whoops;

use InvalidArgumentException;
use Whoops\Exception\ErrorException;
use Whoops\Handler\HandlerInterface;

interface RunInterface
{
const EXCEPTION_HANDLER = "handleException";
const ERROR_HANDLER = "handleError";
const SHUTDOWN_HANDLER = "handleShutdown";








public function pushHandler($handler);







public function popHandler();







public function getHandlers();







public function clearHandlers();




public function getFrameFilters();




public function clearFrameFilters();






public function register();






public function unregister();







public function allowQuit($exit = null);








public function silenceErrorsInPaths($patterns, $levels = 10240);









public function sendHttpCode($code = null);








public function sendExitCode($code = null);








public function writeToOutput($send = null);








public function handleException($exception);















public function handleError($level, $message, $file = null, $line = null);




public function handleShutdown();







public function addFrameFilter($filterCallback);
}
