<?php

















return array_replace_recursive(require __DIR__.'/en.php', [
'from_now' => 'in :time',
'formats' => [
'LT' => 'h:mm A',
'LTS' => 'h:mm:ss A',
'L' => 'DD/MM/YYYY',
'LL' => 'D MMMM YYYY',
'LLL' => 'D MMMM YYYY h:mm A',
'LLLL' => 'dddd, D MMMM YYYY h:mm A',
],
'day_of_first_week_of_year' => 4,
]);
