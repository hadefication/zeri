<?php










namespace Symfony\Component\VarDumper\Caster;






class UninitializedStub extends ConstStub
{
public function __construct(\ReflectionProperty $property)
{
parent::__construct('?'.($property->hasType() ? ' '.$property->getType() : ''), 'Uninitialized property');
}
}
