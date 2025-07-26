<?php

declare(strict_types=1);










namespace Carbon;

use DateTimeInterface;

interface CarbonConverterInterface
{
public function convertDate(DateTimeInterface $dateTime, bool $negated = false): CarbonInterface;
}
