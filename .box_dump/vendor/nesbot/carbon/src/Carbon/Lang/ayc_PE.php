<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'DD/MM/YY',
],
'months' => ['inïru', 'phiwriru', 'marsu', 'awrila', 'mayu', 'junyu', 'julyu', 'awustu', 'sitimri', 'uktuwri', 'nuwimri', 'risimri'],
'months_short' => ['ini', 'phi', 'mar', 'awr', 'may', 'jun', 'jul', 'awu', 'sit', 'ukt', 'nuw', 'ris'],
'weekdays' => ['tuminku', 'lunisa', 'martisa', 'mirkulisa', 'juywisa', 'wirnisa', 'sawäru'],
'weekdays_short' => ['tum', 'lun', 'mar', 'mir', 'juy', 'wir', 'saw'],
'weekdays_min' => ['tum', 'lun', 'mar', 'mir', 'juy', 'wir', 'saw'],
'first_day_of_week' => 0,
'day_of_first_week_of_year' => 1,
'meridiem' => ['VM', 'NM'],
]);
