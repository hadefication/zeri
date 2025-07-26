<?php










namespace Symfony\Component\VarDumper\Caster;

use Symfony\Component\VarDumper\Cloner\Stub;




class DsPairStub extends Stub
{
public function __construct(mixed $key, mixed $value)
{
$this->value = [
Caster::PREFIX_VIRTUAL.'key' => $key,
Caster::PREFIX_VIRTUAL.'value' => $value,
];
}
}
