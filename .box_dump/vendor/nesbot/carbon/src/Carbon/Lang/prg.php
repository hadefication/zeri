<?php










return array_replace_recursive(require __DIR__.'/en.php', [
'months' => ['M01', 'M02', 'M03', 'M04', 'M05', 'M06', 'M07', 'M08', 'M09', 'M10', 'M11', 'M12'],
'months_short' => ['M01', 'M02', 'M03', 'M04', 'M05', 'M06', 'M07', 'M08', 'M09', 'M10', 'M11', 'M12'],
'first_day_of_week' => 1,
'formats' => [
'LT' => 'HH:mm',
'LTS' => 'HH:mm:ss',
'L' => 'YYYY-MM-dd',
'LL' => 'YYYY MMM D',
'LLL' => 'YYYY MMMM D HH:mm',
'LLLL' => 'YYYY MMMM D, dddd HH:mm',
],

'year' => ':count meta',
'y' => ':count meta',
'a_year' => ':count meta',

'month' => ':count mēniks', 
'm' => ':count mēniks', 
'a_month' => ':count mēniks', 

'week' => ':count sawaītin', 
'w' => ':count sawaītin', 
'a_week' => ':count sawaītin', 

'day' => ':count di',
'd' => ':count di',
'a_day' => ':count di',

'hour' => ':count bruktēt', 
'h' => ':count bruktēt', 
'a_hour' => ':count bruktēt', 

'minute' => ':count līkuts', 
'min' => ':count līkuts', 
'a_minute' => ':count līkuts', 

'second' => ':count kitan', 
's' => ':count kitan', 
'a_second' => ':count kitan', 
]);
