<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'DD/MM/YY',
],
'months' => ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'],
'months_short' => ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'],
'weekdays' => ['teoilhuitl', 'ceilhuitl', 'omeilhuitl', 'yeilhuitl', 'nahuilhuitl', 'macuililhuitl', 'chicuaceilhuitl'],
'weekdays_short' => ['teo', 'cei', 'ome', 'yei', 'nau', 'mac', 'chi'],
'weekdays_min' => ['teo', 'cei', 'ome', 'yei', 'nau', 'mac', 'chi'],
'first_day_of_week' => 0,
'day_of_first_week_of_year' => 1,

'month' => ':count metztli', 
'm' => ':count metztli', 
'a_month' => ':count metztli', 

'week' => ':count tonalli', 
'w' => ':count tonalli', 
'a_week' => ':count tonalli', 

'day' => ':count tonatih', 
'd' => ':count tonatih', 
'a_day' => ':count tonatih', 

'minute' => ':count toltecayotl', 
'min' => ':count toltecayotl', 
'a_minute' => ':count toltecayotl', 

'second' => ':count ome', 
's' => ':count ome', 
'a_second' => ':count ome', 

'year' => ':count xihuitl',
'y' => ':count xihuitl',
'a_year' => ':count xihuitl',
]);
