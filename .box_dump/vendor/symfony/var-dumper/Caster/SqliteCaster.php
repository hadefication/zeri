<?php










namespace Symfony\Component\VarDumper\Caster;

use Symfony\Component\VarDumper\Cloner\Stub;






final class SqliteCaster
{
public static function castSqlite3Result(\SQLite3Result $result, array $a, Stub $stub, bool $isNested): array
{
$numColumns = $result->numColumns();
for ($i = 0; $i < $numColumns; ++$i) {
$a[Caster::PREFIX_VIRTUAL.'columnNames'][$i] = $result->columnName($i);
}

return $a;
}
}
