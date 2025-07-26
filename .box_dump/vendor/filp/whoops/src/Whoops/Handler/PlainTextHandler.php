<?php







namespace Whoops\Handler;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Whoops\Exception\Frame;






class PlainTextHandler extends Handler
{
const VAR_DUMP_PREFIX = '   | ';




protected $logger;




protected $dumper;




private $addTraceToOutput = true;




private $addTraceFunctionArgsToOutput = false;




private $traceFunctionArgsOutputLimit = 1024;




private $addPreviousToOutput = true;




private $loggerOnly = false;






public function __construct($logger = null)
{
$this->setLogger($logger);
}






public function setLogger($logger = null)
{
if (! (is_null($logger)
|| $logger instanceof LoggerInterface)) {
throw new InvalidArgumentException(
'Argument to ' . __METHOD__ .
" must be a valid Logger Interface (aka. Monolog), " .
get_class($logger) . ' given.'
);
}

$this->logger = $logger;
}




public function getLogger()
{
return $this->logger;
}







public function setDumper(callable $dumper)
{
$this->dumper = $dumper;
return $this;
}






public function addTraceToOutput($addTraceToOutput = null)
{
if (func_num_args() == 0) {
return $this->addTraceToOutput;
}

$this->addTraceToOutput = (bool) $addTraceToOutput;
return $this;
}






public function addPreviousToOutput($addPreviousToOutput = null)
{
if (func_num_args() == 0) {
return $this->addPreviousToOutput;
}

$this->addPreviousToOutput = (bool) $addPreviousToOutput;
return $this;
}







public function addTraceFunctionArgsToOutput($addTraceFunctionArgsToOutput = null)
{
if (func_num_args() == 0) {
return $this->addTraceFunctionArgsToOutput;
}

if (! is_integer($addTraceFunctionArgsToOutput)) {
$this->addTraceFunctionArgsToOutput = (bool) $addTraceFunctionArgsToOutput;
} else {
$this->addTraceFunctionArgsToOutput = $addTraceFunctionArgsToOutput;
}
return $this;
}








public function setTraceFunctionArgsOutputLimit($traceFunctionArgsOutputLimit)
{
$this->traceFunctionArgsOutputLimit = (integer) $traceFunctionArgsOutputLimit;
return $this;
}





public function generateResponse()
{
$exception = $this->getException();
$message = $this->getExceptionOutput($exception);

if ($this->addPreviousToOutput) {
$previous = $exception->getPrevious();
while ($previous) {
$message .= "\n\nCaused by\n" . $this->getExceptionOutput($previous);
$previous = $previous->getPrevious();
}
}


return $message . $this->getTraceOutput() . "\n";
}







public function getTraceFunctionArgsOutputLimit()
{
return $this->traceFunctionArgsOutputLimit;
}






public function loggerOnly($loggerOnly = null)
{
if (func_num_args() == 0) {
return $this->loggerOnly;
}

$this->loggerOnly = (bool) $loggerOnly;
return $this;
}





private function canOutput()
{
return !$this->loggerOnly();
}







private function getFrameArgsOutput(Frame $frame, $line)
{
if ($this->addTraceFunctionArgsToOutput() === false
|| $this->addTraceFunctionArgsToOutput() < $line) {
return '';
}


ob_start();
$this->dump($frame->getArgs());
if (ob_get_length() > $this->getTraceFunctionArgsOutputLimit()) {


ob_clean();
return sprintf(
"\n%sArguments dump length greater than %d Bytes. Discarded.",
self::VAR_DUMP_PREFIX,
$this->getTraceFunctionArgsOutputLimit()
);
}

return sprintf(
"\n%s",
preg_replace('/^/m', self::VAR_DUMP_PREFIX, ob_get_clean())
);
}







protected function dump($var)
{
if ($this->dumper) {
call_user_func($this->dumper, $var);
} else {
var_dump($var);
}
}





private function getTraceOutput()
{
if (! $this->addTraceToOutput()) {
return '';
}
$inspector = $this->getInspector();
$frames = $inspector->getFrames($this->getRun()->getFrameFilters());

$response = "\nStack trace:";

$line = 1;
foreach ($frames as $frame) {

$class = $frame->getClass();

$template = "\n%3d. %s->%s() %s:%d%s";
if (! $class) {

$template = "\n%3d. %s%s() %s:%d%s";
}

$response .= sprintf(
$template,
$line,
$class,
$frame->getFunction(),
$frame->getFile(),
$frame->getLine(),
$this->getFrameArgsOutput($frame, $line)
);

$line++;
}

return $response;
}






private function getExceptionOutput($exception)
{
return sprintf(
"%s: %s in file %s on line %d",
get_class($exception),
$exception->getMessage(),
$exception->getFile(),
$exception->getLine()
);
}




public function handle()
{
$response = $this->generateResponse();

if ($this->getLogger()) {
$this->getLogger()->error($response);
}

if (! $this->canOutput()) {
return Handler::DONE;
}

echo $response;

return Handler::QUIT;
}




public function contentType()
{
return 'text/plain';
}
}
