<?php










namespace Symfony\Component\VarDumper\Caster;

use ProxyManager\Proxy\ProxyInterface;
use Symfony\Component\VarDumper\Cloner\Stub;








class ProxyManagerCaster
{
public static function castProxy(ProxyInterface $c, array $a, Stub $stub, bool $isNested): array
{
if ($parent = get_parent_class($c)) {
$stub->class .= ' - '.$parent;
}
$stub->class .= '@proxy';

return $a;
}
}
