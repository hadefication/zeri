<?php










namespace Symfony\Component\VarDumper\Exception;




class ThrowingCasterException extends \Exception
{



public function __construct(\Throwable $prev)
{
parent::__construct('Unexpected '.$prev::class.' thrown from a caster: '.$prev->getMessage(), 0, $prev);
}
}
