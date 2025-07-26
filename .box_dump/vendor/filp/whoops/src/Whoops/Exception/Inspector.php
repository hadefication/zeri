<?php





namespace Whoops\Exception;

use Whoops\Inspector\InspectorFactory;
use Whoops\Inspector\InspectorInterface;
use Whoops\Util\Misc;

class Inspector implements InspectorInterface
{



private $exception;




private $frames;




private $previousExceptionInspector;




private $previousExceptions;




protected $inspectorFactory;





public function __construct($exception, $factory = null)
{
$this->exception = $exception;
$this->inspectorFactory = $factory ?: new InspectorFactory();
}




public function getException()
{
return $this->exception;
}




public function getExceptionName()
{
return get_class($this->exception);
}




public function getExceptionMessage()
{
return $this->extractDocrefUrl($this->exception->getMessage())['message'];
}




public function getPreviousExceptionMessages()
{
return array_map(function ($prev) {

return $this->extractDocrefUrl($prev->getMessage())['message'];
}, $this->getPreviousExceptions());
}




public function getPreviousExceptionCodes()
{
return array_map(function ($prev) {

return $prev->getCode();
}, $this->getPreviousExceptions());
}






public function getExceptionDocrefUrl()
{
return $this->extractDocrefUrl($this->exception->getMessage())['url'];
}

private function extractDocrefUrl($message)
{
$docref = [
'message' => $message,
'url' => null,
];



if (!ini_get('html_errors') || !ini_get('docref_root')) {
return $docref;
}

$pattern = "/\[<a href='([^']+)'>(?:[^<]+)<\/a>\]/";
if (preg_match($pattern, $message, $matches)) {

$docref['message'] = preg_replace($pattern, '', $message, 1);
$docref['url'] = $matches[1];
}

return $docref;
}





public function hasPreviousException()
{
return $this->previousExceptionInspector || $this->exception->getPrevious();
}






public function getPreviousExceptionInspector()
{
if ($this->previousExceptionInspector === null) {
$previousException = $this->exception->getPrevious();

if ($previousException) {
$this->previousExceptionInspector = $this->inspectorFactory->create($previousException);
}
}

return $this->previousExceptionInspector;
}






public function getPreviousExceptions()
{
if ($this->previousExceptions === null) {
$this->previousExceptions = [];

$prev = $this->exception->getPrevious();
while ($prev !== null) {
$this->previousExceptions[] = $prev;
$prev = $prev->getPrevious();
}
}

return $this->previousExceptions;
}









public function getFrames(array $frameFilters = [])
{
if ($this->frames === null) {
$frames = $this->getTrace($this->exception);


foreach ($frames as $k => $frame) {
if (empty($frame['file'])) {

$file = '[internal]';
$line = 0;

$next_frame = !empty($frames[$k + 1]) ? $frames[$k + 1] : [];

if ($this->isValidNextFrame($next_frame)) {
$file = $next_frame['file'];
$line = $next_frame['line'];
}

$frames[$k]['file'] = $file;
$frames[$k]['line'] = $line;
}
}


$i = 0;
foreach ($frames as $k => $frame) {
if ($frame['file'] == $this->exception->getFile() && $frame['line'] == $this->exception->getLine()) {
$i = $k;
}
}


if ($i > 0) {
array_splice($frames, 0, $i);
}

$firstFrame = $this->getFrameFromException($this->exception);
array_unshift($frames, $firstFrame);

$this->frames = new FrameCollection($frames);

if ($previousInspector = $this->getPreviousExceptionInspector()) {

$outerFrames = $this->frames;
$newFrames = clone $previousInspector->getFrames();

if (isset($newFrames[0])) {
$newFrames[0]->addComment(
$previousInspector->getExceptionMessage(),
'Exception message:'
);
}
$newFrames->prependFrames($outerFrames->topDiff($newFrames));
$this->frames = $newFrames;
}


if (!empty($frameFilters)) {
foreach ($frameFilters as $filterCallback) {
$this->frames->filter($filterCallback);
}
}
}

return $this->frames;
}









protected function getTrace($e)
{
$traces = $e->getTrace();


if (!$e instanceof \ErrorException) {
return $traces;
}

if (!Misc::isLevelFatal($e->getSeverity())) {
return $traces;
}

if (!extension_loaded('xdebug') || !function_exists('xdebug_is_enabled') || !xdebug_is_enabled()) {
return $traces;
}


$stack = array_reverse(xdebug_get_function_stack());
$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
$traces = array_diff_key($stack, $trace);

return $traces;
}







protected function getFrameFromException($exception)
{
return [
'file' => $exception->getFile(),
'line' => $exception->getLine(),
'class' => get_class($exception),
'args' => [
$exception->getMessage(),
],
];
}







protected function getFrameFromError(ErrorException $exception)
{
return [
'file' => $exception->getFile(),
'line' => $exception->getLine(),
'class' => null,
'args' => [],
];
}







protected function isValidNextFrame(array $frame)
{
if (empty($frame['file'])) {
return false;
}

if (empty($frame['line'])) {
return false;
}

if (empty($frame['function']) || !stristr($frame['function'], 'call_user_func')) {
return false;
}

return true;
}
}
