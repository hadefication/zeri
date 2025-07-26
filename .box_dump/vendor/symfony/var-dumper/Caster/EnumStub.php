<?php










namespace Symfony\Component\VarDumper\Caster;

use Symfony\Component\VarDumper\Cloner\Stub;






class EnumStub extends Stub
{
public function __construct(
array $values,
public bool $dumpKeys = true,
) {
$this->value = $values;
}
}
