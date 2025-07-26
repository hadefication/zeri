<?php





namespace Whoops\Exception;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use ReturnTypeWillChange;
use Serializable;
use UnexpectedValueException;





class FrameCollection implements ArrayAccess, IteratorAggregate, Serializable, Countable
{



private $frames;

public function __construct(array $frames)
{
$this->frames = array_map(function ($frame) {
return new Frame($frame);
}, $frames);
}







public function filter($callable)
{
$this->frames = array_values(array_filter($this->frames, $callable));
return $this;
}







public function map($callable)
{


$this->frames = array_map(function ($frame) use ($callable) {
$frame = call_user_func($callable, $frame);

if (!$frame instanceof Frame) {
throw new UnexpectedValueException(
"Callable to " . __CLASS__ . "::map must return a Frame object"
);
}

return $frame;
}, $this->frames);

return $this;
}










public function getArray()
{
return $this->frames;
}





#[ReturnTypeWillChange]
public function getIterator()
{
return new ArrayIterator($this->frames);
}





#[ReturnTypeWillChange]
public function offsetExists($offset)
{
return isset($this->frames[$offset]);
}





#[ReturnTypeWillChange]
public function offsetGet($offset)
{
return $this->frames[$offset];
}





#[ReturnTypeWillChange]
public function offsetSet($offset, $value)
{
throw new \Exception(__CLASS__ . ' is read only');
}





#[ReturnTypeWillChange]
public function offsetUnset($offset)
{
throw new \Exception(__CLASS__ . ' is read only');
}





#[ReturnTypeWillChange]
public function count()
{
return count($this->frames);
}






public function countIsApplication()
{
return count(array_filter($this->frames, function (Frame $f) {
return $f->isApplication();
}));
}





#[ReturnTypeWillChange]
public function serialize()
{
return serialize($this->frames);
}





#[ReturnTypeWillChange]
public function unserialize($serializedFrames)
{
$this->frames = unserialize($serializedFrames);
}

public function __serialize()
{
return $this->frames;
}

public function __unserialize(array $serializedFrames)
{
$this->frames = $serializedFrames;
}




public function prependFrames(array $frames)
{
$this->frames = array_merge($frames, $this->frames);
}







public function topDiff(FrameCollection $parentFrames)
{
$diff = $this->frames;

$parentFrames = $parentFrames->getArray();
$p = count($parentFrames)-1;

for ($i = count($diff)-1; $i >= 0 && $p >= 0; $i--) {

$tailFrame = $diff[$i];
if ($tailFrame->equals($parentFrames[$p])) {
unset($diff[$i]);
}
$p--;
}
return $diff;
}
}
