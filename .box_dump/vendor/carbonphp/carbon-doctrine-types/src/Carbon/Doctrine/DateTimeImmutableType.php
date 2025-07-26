<?php

declare(strict_types=1);

namespace Carbon\Doctrine;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\VarDateTimeImmutableType;

class DateTimeImmutableType extends VarDateTimeImmutableType implements CarbonDoctrineType
{
/**
@use */
use CarbonTypeConverter;




public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?CarbonImmutable
{
return $this->doConvertToPHPValue($value);
}




protected function getCarbonClassName(): string
{
return CarbonImmutable::class;
}
}
