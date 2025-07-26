<?php










return array_replace_recursive(require __DIR__.'/en.php', [
'meridiem' => ['ND', 'LK'],
'weekdays' => ['Bikua-ôko', 'Bïkua-ûse', 'Bïkua-ptâ', 'Bïkua-usïö', 'Bïkua-okü', 'Lâpôsö', 'Lâyenga'],
'weekdays_short' => ['Bk1', 'Bk2', 'Bk3', 'Bk4', 'Bk5', 'Lâp', 'Lây'],
'weekdays_min' => ['Bk1', 'Bk2', 'Bk3', 'Bk4', 'Bk5', 'Lâp', 'Lây'],
'months' => ['Nyenye', 'Fulundïgi', 'Mbängü', 'Ngubùe', 'Bêläwü', 'Föndo', 'Lengua', 'Kükürü', 'Mvuka', 'Ngberere', 'Nabändüru', 'Kakauka'],
'months_short' => ['Nye', 'Ful', 'Mbä', 'Ngu', 'Bêl', 'Fön', 'Len', 'Kük', 'Mvu', 'Ngb', 'Nab', 'Kak'],
'first_day_of_week' => 1,
'formats' => [
'LT' => 'HH:mm',
'LTS' => 'HH:mm:ss',
'L' => 'D/M/YYYY',
'LL' => 'D MMM, YYYY',
'LLL' => 'D MMMM YYYY HH:mm',
'LLLL' => 'dddd D MMMM YYYY HH:mm',
],

'year' => ':count dā', 
'y' => ':count dā', 
'a_year' => ':count dā', 

'week' => ':count bïkua-okü', 
'w' => ':count bïkua-okü', 
'a_week' => ':count bïkua-okü', 

'day' => ':count ziggawâ', 
'd' => ':count ziggawâ', 
'a_day' => ':count ziggawâ', 

'hour' => ':count yângâködörö', 
'h' => ':count yângâködörö', 
'a_hour' => ':count yângâködörö', 

'second' => ':count bïkua-ôko', 
's' => ':count bïkua-ôko', 
'a_second' => ':count bïkua-ôko', 

'month' => ':count Nze tî ngu',
'm' => ':count Nze tî ngu',
'a_month' => ':count Nze tî ngu',
]);
