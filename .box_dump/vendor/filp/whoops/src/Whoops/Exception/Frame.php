<?php





namespace Whoops\Exception;

use InvalidArgumentException;
use Serializable;

class Frame implements Serializable
{



protected $frame;




protected $fileContentsCache;




protected $comments = [];




protected $application;

public function __construct(array $frame)
{
$this->frame = $frame;
}





public function getFile($shortened = false)
{
if (empty($this->frame['file'])) {
return null;
}

$file = $this->frame['file'];





if (preg_match('/^(.*)\((\d+)\) : (?:eval\(\)\'d|assert) code$/', $file, $matches)) {
$file = $this->frame['file'] = $matches[1];
$this->frame['line'] = (int) $matches[2];
}

if ($shortened && is_string($file)) {

$dirname = dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))));
if ($dirname !== '/') {
$file = str_replace($dirname, "&hellip;", $file);
}
$file = str_replace("/", "/&shy;", $file);
}

return $file;
}




public function getLine()
{
return isset($this->frame['line']) ? $this->frame['line'] : null;
}




public function getClass()
{
return isset($this->frame['class']) ? $this->frame['class'] : null;
}




public function getFunction()
{
return isset($this->frame['function']) ? $this->frame['function'] : null;
}




public function getArgs()
{
return isset($this->frame['args']) ? (array) $this->frame['args'] : [];
}






public function getFileContents()
{
if ($this->fileContentsCache === null && $filePath = $this->getFile()) {



if ($filePath === "Unknown" || $filePath === '[internal]') {
return null;
}

try {
$this->fileContentsCache = file_get_contents($filePath);
} catch (ErrorException $exception) {

}
}

return $this->fileContentsCache;
}












public function addComment($comment, $context = 'global')
{
$this->comments[] = [
'comment' => $comment,
'context' => $context,
];
}









public function getComments($filter = null)
{
$comments = $this->comments;

if ($filter !== null) {
$comments = array_filter($comments, function ($c) use ($filter) {
return $c['context'] == $filter;
});
}

return $comments;
}







public function getRawFrame()
{
return $this->frame;
}



















public function getFileLines($start = 0, $length = null)
{
if (null !== ($contents = $this->getFileContents())) {
$lines = explode("\n", $contents);


if ($length !== null) {
$start = (int) $start;
$length = (int) $length;
if ($start < 0) {
$start = 0;
}

if ($length <= 0) {
throw new InvalidArgumentException(
"\$length($length) cannot be lower or equal to 0"
);
}

$lines = array_slice($lines, $start, $length, true);
}

return $lines;
}
}








public function serialize()
{
$frame = $this->frame;
if (!empty($this->comments)) {
$frame['_comments'] = $this->comments;
}

return serialize($frame);
}

public function __serialize()
{
$frame = $this->frame;
if (!empty($this->comments)) {
$frame['_comments'] = $this->comments;
}
return $frame;
}








public function unserialize($serializedFrame)
{
$frame = unserialize($serializedFrame);

if (!empty($frame['_comments'])) {
$this->comments = $frame['_comments'];
unset($frame['_comments']);
}

$this->frame = $frame;
}

public function __unserialize($frame)
{
if (!empty($frame['_comments'])) {
$this->comments = $frame['_comments'];
unset($frame['_comments']);
}

$this->frame = $frame;
}






public function equals(Frame $frame)
{
if (!$this->getFile() || $this->getFile() === 'Unknown' || !$this->getLine()) {
return false;
}
return $frame->getFile() === $this->getFile() && $frame->getLine() === $this->getLine();
}






public function isApplication()
{
return $this->application;
}






public function setApplication($application)
{
$this->application = $application;
}
}
