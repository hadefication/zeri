<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'DD/MM/YY',
],
'months' => ['Pellkwet̓min', 'Pelctsipwen̓ten', 'Pellsqépts', 'Peslléwten', 'Pell7ell7é7llqten', 'Pelltspéntsk', 'Pelltqwelq̓wél̓t', 'Pellct̓éxel̓cten', 'Pesqelqlélten', 'Pesllwélsten', 'Pellc7ell7é7llcwten̓', 'Pelltetétq̓em'],
'months_short' => ['Kwe', 'Tsi', 'Sqe', 'Éwt', 'Ell', 'Tsp', 'Tqw', 'Ct̓é', 'Qel', 'Wél', 'U7l', 'Tet'],
'weekdays' => ['Sxetspesq̓t', 'Spetkesq̓t', 'Selesq̓t', 'Skellesq̓t', 'Smesesq̓t', 'Stselkstesq̓t', 'Stqmekstesq̓t'],
'weekdays_short' => ['Sxe', 'Spe', 'Sel', 'Ske', 'Sme', 'Sts', 'Stq'],
'weekdays_min' => ['Sxe', 'Spe', 'Sel', 'Ske', 'Sme', 'Sts', 'Stq'],
'first_day_of_week' => 0,
'day_of_first_week_of_year' => 1,

'year' => ':count sqlélten', 
'y' => ':count sqlélten', 
'a_year' => ':count sqlélten', 

'month' => ':count swewll', 
'm' => ':count swewll', 
'a_month' => ':count swewll', 

'hour' => ':count seqwlút', 
'h' => ':count seqwlút', 
'a_hour' => ':count seqwlút', 
]);
