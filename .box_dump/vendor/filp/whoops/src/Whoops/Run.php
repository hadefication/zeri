<?php





namespace Whoops;

use InvalidArgumentException;
use Throwable;
use Whoops\Exception\ErrorException;
use Whoops\Handler\CallbackHandler;
use Whoops\Handler\Handler;
use Whoops\Handler\HandlerInterface;
use Whoops\Inspector\CallableInspectorFactory;
use Whoops\Inspector\InspectorFactory;
use Whoops\Inspector\InspectorFactoryInterface;
use Whoops\Inspector\InspectorInterface;
use Whoops\Util\Misc;
use Whoops\Util\SystemFacade;

final class Run implements RunInterface
{



private $isRegistered;




private $allowQuit = true;




private $sendOutput = true;




private $sendHttpCode = 500;




private $sendExitCode = 1;




private $handlerStack = [];

/**
@psalm-var

*/
private $silencedPatterns = [];




private $system;






private $canThrowExceptions = true;






private $inspectorFactory;




private $frameFilters = [];

public function __construct(?SystemFacade $system = null)
{
$this->system = $system ?: new SystemFacade;
$this->inspectorFactory = new InspectorFactory();
}

public function __destruct()
{
$this->unregister();
}








public function appendHandler($handler)
{
array_unshift($this->handlerStack, $this->resolveHandler($handler));
return $this;
}








public function prependHandler($handler)
{
return $this->pushHandler($handler);
}











public function pushHandler($handler)
{
$this->handlerStack[] = $this->resolveHandler($handler);
return $this;
}








public function popHandler()
{
return array_pop($this->handlerStack);
}






public function removeFirstHandler()
{
array_pop($this->handlerStack);
}






public function removeLastHandler()
{
array_shift($this->handlerStack);
}






public function getHandlers()
{
return $this->handlerStack;
}






public function clearHandlers()
{
$this->handlerStack = [];
return $this;
}

public function getFrameFilters()
{
return $this->frameFilters;
}

public function clearFrameFilters()
{
$this->frameFilters = [];
return $this;
}






public function register()
{
if (!$this->isRegistered) {


class_exists("\\Whoops\\Exception\\ErrorException");
class_exists("\\Whoops\\Exception\\FrameCollection");
class_exists("\\Whoops\\Exception\\Frame");
class_exists("\\Whoops\\Exception\\Inspector");
class_exists("\\Whoops\\Inspector\\InspectorFactory");

$this->system->setErrorHandler([$this, self::ERROR_HANDLER]);
$this->system->setExceptionHandler([$this, self::EXCEPTION_HANDLER]);
$this->system->registerShutdownFunction([$this, self::SHUTDOWN_HANDLER]);

$this->isRegistered = true;
}

return $this;
}






public function unregister()
{
if ($this->isRegistered) {
$this->system->restoreExceptionHandler();
$this->system->restoreErrorHandler();

$this->isRegistered = false;
}

return $this;
}








public function allowQuit($exit = null)
{
if (func_num_args() == 0) {
return $this->allowQuit;
}

return $this->allowQuit = (bool) $exit;
}









public function silenceErrorsInPaths($patterns, $levels = 10240)
{
$this->silencedPatterns = array_merge(
$this->silencedPatterns,
array_map(
function ($pattern) use ($levels) {
return [
"pattern" => $pattern,
"levels" => $levels,
];
},
(array) $patterns
)
);

return $this;
}






public function getSilenceErrorsInPaths()
{
return $this->silencedPatterns;
}












public function sendHttpCode($code = null)
{
if (func_num_args() == 0) {
return $this->sendHttpCode;
}

if (!$code) {
return $this->sendHttpCode = false;
}

if ($code === true) {
$code = 500;
}

if ($code < 400 || 600 <= $code) {
throw new InvalidArgumentException(
"Invalid status code '$code', must be 4xx or 5xx"
);
}

return $this->sendHttpCode = $code;
}











public function sendExitCode($code = null)
{
if (func_num_args() == 0) {
return $this->sendExitCode;
}

if ($code < 0 || 255 <= $code) {
throw new InvalidArgumentException(
"Invalid status code '$code', must be between 0 and 254"
);
}

return $this->sendExitCode = (int) $code;
}









public function writeToOutput($send = null)
{
if (func_num_args() == 0) {
return $this->sendOutput;
}

return $this->sendOutput = (bool) $send;
}








public function handleException($exception)
{


$inspector = $this->getInspector($exception);




$this->system->startOutputBuffering();


$handlerResponse = null;
$handlerContentType = null;

try {
foreach (array_reverse($this->handlerStack) as $handler) {
$handler->setRun($this);
$handler->setInspector($inspector);
$handler->setException($exception);





$handlerResponse = $handler->handle($exception);


$handlerContentType = method_exists($handler, 'contentType') ? $handler->contentType() : null;

if (in_array($handlerResponse, [Handler::LAST_HANDLER, Handler::QUIT])) {




break;
}
}

$willQuit = $handlerResponse == Handler::QUIT && $this->allowQuit();
} finally {
$output = $this->system->cleanOutputBuffer();
}




if ($this->writeToOutput()) {

if ($willQuit) {

while ($this->system->getOutputBufferLevel() > 0) {
$this->system->endOutputBuffering();
}


if (Misc::canSendHeaders() && $handlerContentType) {
header("Content-Type: {$handlerContentType}");
}
}

$this->writeToOutputNow($output);
}

if ($willQuit) {

$this->system->flushOutputBuffer();

$this->system->stopExecution(
$this->sendExitCode()
);
}

return $output;
}















public function handleError($level, $message, $file = null, $line = null)
{
if ($level & $this->system->getErrorReportingLevel()) {
foreach ($this->silencedPatterns as $entry) {
$pathMatches = (bool) preg_match($entry["pattern"], $file);
$levelMatches = $level & $entry["levels"];
if ($pathMatches && $levelMatches) {


return true;
}
}



$exception = new ErrorException($message,  $level,  $level, $file, $line);
if ($this->canThrowExceptions) {
throw $exception;
} else {
$this->handleException($exception);
}

return true;
}



return false;
}






public function handleShutdown()
{



$this->canThrowExceptions = false;


if (!$this->isRegistered) {
return;
}

$error = $this->system->getLastError();
if ($error && Misc::isLevelFatal($error['type'])) {


$this->allowQuit = false;
$this->handleError(
$error['type'],
$error['message'],
$error['file'],
$error['line']
);
}
}







public function setInspectorFactory(InspectorFactoryInterface $factory)
{
$this->inspectorFactory = $factory;
}

public function addFrameFilter($filterCallback)
{
if (!is_callable($filterCallback)) {
throw new \InvalidArgumentException(sprintf(
"A frame filter must be of type callable, %s type given.",
gettype($filterCallback)
));
}

$this->frameFilters[] = $filterCallback;
return $this;
}






private function getInspector($exception)
{
return $this->inspectorFactory->create($exception);
}










private function resolveHandler($handler)
{
if (is_callable($handler)) {
$handler = new CallbackHandler($handler);
}

if (!$handler instanceof HandlerInterface) {
throw new InvalidArgumentException(
"Handler must be a callable, or instance of "
. "Whoops\\Handler\\HandlerInterface"
);
}

return $handler;
}








private function writeToOutputNow($output)
{
if ($this->sendHttpCode() && Misc::canSendHeaders()) {
$this->system->setHttpResponseCode(
$this->sendHttpCode()
);
}

echo $output;

return $this;
}
}
