<?php










namespace Symfony\Component\VarDumper\Cloner;




interface ClonerInterface
{



public function cloneVar(mixed $var): Data;
}
