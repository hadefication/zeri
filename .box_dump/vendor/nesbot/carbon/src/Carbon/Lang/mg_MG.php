<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'DD.MM.YYYY',
],
'months' => ['Janoary', 'Febroary', 'Martsa', 'Aprily', 'Mey', 'Jona', 'Jolay', 'Aogositra', 'Septambra', 'Oktobra', 'Novambra', 'Desambra'],
'months_short' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mey', 'Jon', 'Jol', 'Aog', 'Sep', 'Okt', 'Nov', 'Des'],
'weekdays' => ['alahady', 'alatsinainy', 'talata', 'alarobia', 'alakamisy', 'zoma', 'sabotsy'],
'weekdays_short' => ['lhd', 'lts', 'tlt', 'lrb', 'lkm', 'zom', 'sab'],
'weekdays_min' => ['lhd', 'lts', 'tlt', 'lrb', 'lkm', 'zom', 'sab'],
'first_day_of_week' => 1,
'day_of_first_week_of_year' => 1,

'minute' => ':count minitra', 
'min' => ':count minitra', 
'a_minute' => ':count minitra', 

'year' => ':count taona',
'y' => ':count taona',
'a_year' => ':count taona',

'month' => ':count volana',
'm' => ':count volana',
'a_month' => ':count volana',

'week' => ':count herinandro',
'w' => ':count herinandro',
'a_week' => ':count herinandro',

'day' => ':count andro',
'd' => ':count andro',
'a_day' => ':count andro',

'hour' => ':count ora',
'h' => ':count ora',
'a_hour' => ':count ora',

'second' => ':count segondra',
's' => ':count segondra',
'a_second' => ':count segondra',
]);
