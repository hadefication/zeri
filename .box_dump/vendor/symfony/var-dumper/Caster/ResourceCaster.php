<?php










namespace Symfony\Component\VarDumper\Caster;

use Symfony\Component\VarDumper\Cloner\Stub;










class ResourceCaster
{



public static function castCurl(\CurlHandle $h, array $a, Stub $stub, bool $isNested): array
{
trigger_deprecation('symfony/var-dumper', '7.3', 'The "%s()" method is deprecated without replacement.', __METHOD__);

return CurlCaster::castCurl($h, $a, $stub, $isNested);
}




public static function castDba(mixed $dba, array $a, Stub $stub, bool $isNested): array
{
if (\PHP_VERSION_ID < 80402 && !\is_resource($dba)) {

return $a;
}

$list = dba_list();
$a['file'] = $list[(int) $dba];

return $a;
}

public static function castProcess($process, array $a, Stub $stub, bool $isNested): array
{
return proc_get_status($process);
}

public static function castStream($stream, array $a, Stub $stub, bool $isNested): array
{
$a = stream_get_meta_data($stream) + static::castStreamContext($stream, $a, $stub, $isNested);
if ($a['uri'] ?? false) {
$a['uri'] = new LinkStub($a['uri']);
}

return $a;
}

public static function castStreamContext($stream, array $a, Stub $stub, bool $isNested): array
{
return @stream_context_get_params($stream) ?: $a;
}




public static function castGd(\GdImage $gd, array $a, Stub $stub, bool $isNested): array
{
trigger_deprecation('symfony/var-dumper', '7.3', 'The "%s()" method is deprecated without replacement.', __METHOD__);

return GdCaster::castGd($gd, $a, $stub, $isNested);
}




public static function castOpensslX509(\OpenSSLCertificate $h, array $a, Stub $stub, bool $isNested): array
{
trigger_deprecation('symfony/var-dumper', '7.3', 'The "%s()" method is deprecated without replacement.', __METHOD__);

return OpenSSLCaster::castOpensslX509($h, $a, $stub, $isNested);
}
}
