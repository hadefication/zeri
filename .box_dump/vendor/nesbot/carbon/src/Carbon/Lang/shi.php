<?php










return array_replace_recursive(require __DIR__.'/en.php', [
'meridiem' => ['ⵜⵉⴼⴰⵡⵜ', 'ⵜⴰⴷⴳⴳⵯⴰⵜ'],
'weekdays' => ['ⴰⵙⴰⵎⴰⵙ', 'ⴰⵢⵏⴰⵙ', 'ⴰⵙⵉⵏⴰⵙ', 'ⴰⴽⵕⴰⵙ', 'ⴰⴽⵡⴰⵙ', 'ⵙⵉⵎⵡⴰⵙ', 'ⴰⵙⵉⴹⵢⴰⵙ'],
'weekdays_short' => ['ⴰⵙⴰ', 'ⴰⵢⵏ', 'ⴰⵙⵉ', 'ⴰⴽⵕ', 'ⴰⴽⵡ', 'ⴰⵙⵉⵎ', 'ⴰⵙⵉⴹ'],
'weekdays_min' => ['ⴰⵙⴰ', 'ⴰⵢⵏ', 'ⴰⵙⵉ', 'ⴰⴽⵕ', 'ⴰⴽⵡ', 'ⴰⵙⵉⵎ', 'ⴰⵙⵉⴹ'],
'months' => ['ⵉⵏⵏⴰⵢⵔ', 'ⴱⵕⴰⵢⵕ', 'ⵎⴰⵕⵚ', 'ⵉⴱⵔⵉⵔ', 'ⵎⴰⵢⵢⵓ', 'ⵢⵓⵏⵢⵓ', 'ⵢⵓⵍⵢⵓⵣ', 'ⵖⵓⵛⵜ', 'ⵛⵓⵜⴰⵏⴱⵉⵔ', 'ⴽⵜⵓⴱⵔ', 'ⵏⵓⵡⴰⵏⴱⵉⵔ', 'ⴷⵓⵊⴰⵏⴱⵉⵔ'],
'months_short' => ['ⵉⵏⵏ', 'ⴱⵕⴰ', 'ⵎⴰⵕ', 'ⵉⴱⵔ', 'ⵎⴰⵢ', 'ⵢⵓⵏ', 'ⵢⵓⵍ', 'ⵖⵓⵛ', 'ⵛⵓⵜ', 'ⴽⵜⵓ', 'ⵏⵓⵡ', 'ⴷⵓⵊ'],
'first_day_of_week' => 6,
'weekend' => [5, 6],
'formats' => [
'LT' => 'HH:mm',
'LTS' => 'HH:mm:ss',
'L' => 'D/M/YYYY',
'LL' => 'D MMM, YYYY',
'LLL' => 'D MMMM YYYY HH:mm',
'LLLL' => 'dddd D MMMM YYYY HH:mm',
],

'year' => ':count aseggwas',
'y' => ':count aseggwas',
'a_year' => ':count aseggwas',

'month' => ':count ayyur',
'm' => ':count ayyur',
'a_month' => ':count ayyur',

'week' => ':count imalass',
'w' => ':count imalass',
'a_week' => ':count imalass',

'day' => ':count ass',
'd' => ':count ass',
'a_day' => ':count ass',

'hour' => ':count urɣ', 
'h' => ':count urɣ', 
'a_hour' => ':count urɣ', 

'minute' => ':count ⴰⵎⵥⵉ', 
'min' => ':count ⴰⵎⵥⵉ', 
'a_minute' => ':count ⴰⵎⵥⵉ', 

'second' => ':count sin', 
's' => ':count sin', 
'a_second' => ':count sin', 
]);
