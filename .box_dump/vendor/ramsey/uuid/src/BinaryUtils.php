<?php











declare(strict_types=1);

namespace Ramsey\Uuid;




class BinaryUtils
{
/**
@pure








*/
public static function applyVariant(int $clockSeq): int
{
return ($clockSeq & 0x3fff) | 0x8000;
}

/**
@pure









*/
public static function applyVersion(int $timeHi, int $version): int
{
return ($timeHi & 0x0fff) | ($version << 12);
}
}
