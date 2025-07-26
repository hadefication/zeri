<?php










namespace Symfony\Component\VarDumper\Caster;

use Symfony\Component\VarDumper\Cloner\Stub;






final class CurlCaster
{
public static function castCurl(\CurlHandle $h, array $a, Stub $stub, bool $isNested): array
{
foreach (curl_getinfo($h) as $key => $val) {
$a[Caster::PREFIX_VIRTUAL.$key] = $val;
}

return $a;
}
}
