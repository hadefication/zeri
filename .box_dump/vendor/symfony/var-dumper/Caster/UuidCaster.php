<?php










namespace Symfony\Component\VarDumper\Caster;

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\VarDumper\Cloner\Stub;






final class UuidCaster
{
public static function castRamseyUuid(UuidInterface $c, array $a, Stub $stub, bool $isNested): array
{
$a += [
Caster::PREFIX_VIRTUAL.'uuid' => (string) $c,
];

return $a;
}
}
