<?php










return array_replace_recursive(require __DIR__.'/en.php', [
'first_day_of_week' => 0,
'meridiem' => ['RŨ', 'ŨG'],
'weekdays' => ['Kiumia', 'Muramuko', 'Wairi', 'Wethatu', 'Wena', 'Wetano', 'Jumamosi'],
'weekdays_short' => ['KIU', 'MRA', 'WAI', 'WET', 'WEN', 'WTN', 'JUM'],
'weekdays_min' => ['KIU', 'MRA', 'WAI', 'WET', 'WEN', 'WTN', 'JUM'],
'months' => ['Januarĩ', 'Feburuarĩ', 'Machi', 'Ĩpurũ', 'Mĩĩ', 'Njuni', 'Njuraĩ', 'Agasti', 'Septemba', 'Oktũba', 'Novemba', 'Dicemba'],
'months_short' => ['JAN', 'FEB', 'MAC', 'ĨPU', 'MĨĨ', 'NJU', 'NJR', 'AGA', 'SPT', 'OKT', 'NOV', 'DEC'],
'formats' => [
'LT' => 'HH:mm',
'LTS' => 'HH:mm:ss',
'L' => 'DD/MM/YYYY',
'LL' => 'D MMM YYYY',
'LLL' => 'D MMMM YYYY HH:mm',
'LLLL' => 'dddd, D MMMM YYYY HH:mm',
],

'year' => ':count murume', 
'y' => ':count murume', 
'a_year' => ':count murume', 

'month' => ':count muchaara', 
'm' => ':count muchaara', 
'a_month' => ':count muchaara', 

'minute' => ':count monto', 
'min' => ':count monto', 
'a_minute' => ':count monto', 

'second' => ':count gikeno', 
's' => ':count gikeno', 
'a_second' => ':count gikeno', 
]);
