<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'first_day_of_week' => 0,
'formats' => [
'L' => 'DD/MM/YYYY',
],
'months' => ['Ianuari', 'Fepuari', 'Mati', 'Aperila', 'Me', 'Iuni', 'Iulai', 'Auguso', 'Setema', 'Oketopa', 'Novema', 'Tesema'],
'months_short' => ['Ian', 'Fep', 'Mat', 'Ape', 'Me', 'Iun', 'Iul', 'Aug', 'Set', 'Oke', 'Nov', 'Tes'],
'weekdays' => ['Aso Sa', 'Aso Gafua', 'Aso Lua', 'Aso Lulu', 'Aso Tofi', 'Aso Farail', 'Aso To\'ana\'i'],
'weekdays_short' => ['Aso Sa', 'Aso Gaf', 'Aso Lua', 'Aso Lul', 'Aso Tof', 'Aso Far', 'Aso To\''],
'weekdays_min' => ['Aso Sa', 'Aso Gaf', 'Aso Lua', 'Aso Lul', 'Aso Tof', 'Aso Far', 'Aso To\''],

'hour' => ':count uati', 
'h' => ':count uati', 
'a_hour' => ':count uati', 

'minute' => ':count itiiti', 
'min' => ':count itiiti', 
'a_minute' => ':count itiiti', 

'second' => ':count lua', 
's' => ':count lua', 
'a_second' => ':count lua', 

'year' => ':count tausaga',
'y' => ':count tausaga',
'a_year' => ':count tausaga',

'month' => ':count māsina',
'm' => ':count māsina',
'a_month' => ':count māsina',

'week' => ':count vaiaso',
'w' => ':count vaiaso',
'a_week' => ':count vaiaso',

'day' => ':count aso',
'd' => ':count aso',
'a_day' => ':count aso',
]);
