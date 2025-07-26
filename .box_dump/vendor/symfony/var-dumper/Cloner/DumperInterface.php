<?php










namespace Symfony\Component\VarDumper\Cloner;






interface DumperInterface
{



public function dumpScalar(Cursor $cursor, string $type, string|int|float|bool|null $value): void;








public function dumpString(Cursor $cursor, string $str, bool $bin, int $cut): void;








public function enterHash(Cursor $cursor, int $type, string|int|null $class, bool $hasChild): void;









public function leaveHash(Cursor $cursor, int $type, string|int|null $class, bool $hasChild, int $cut): void;
}
