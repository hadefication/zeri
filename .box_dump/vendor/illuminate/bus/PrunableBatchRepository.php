<?php

namespace Illuminate\Bus;

use DateTimeInterface;

interface PrunableBatchRepository extends BatchRepository
{






public function prune(DateTimeInterface $before);
}
