<?php

declare(strict_types=1);

namespace Carbon\Doctrine;

use Carbon\Carbon;
use DateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\VarDateTimeType;

class DateTimeType extends VarDateTimeType implements CarbonDoctrineType
{
/**
@use */
use CarbonTypeConverter;




public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Carbon
{
return $this->doConvertToPHPValue($value);
}
}
