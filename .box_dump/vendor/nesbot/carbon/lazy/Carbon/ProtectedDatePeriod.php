<?php










namespace Carbon;

use DatePeriod;

if (!class_exists(DatePeriodBase::class, false)) {
class DatePeriodBase extends DatePeriod
{







protected $start;








protected $end;








protected $current;








protected $interval;








protected $recurrences;








protected $include_start_date;
}
}
