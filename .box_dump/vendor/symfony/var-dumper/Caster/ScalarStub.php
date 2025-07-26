<?php










namespace Symfony\Component\VarDumper\Caster;

use Symfony\Component\VarDumper\Cloner\Stub;






class ScalarStub extends Stub
{
public function __construct(mixed $value)
{
$this->value = $value;
}
}
