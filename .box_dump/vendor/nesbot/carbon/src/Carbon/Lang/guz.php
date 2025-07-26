<?php










return array_replace_recursive(require __DIR__.'/en.php', [
'first_day_of_week' => 0,
'meridiem' => ['Ma', 'Mo'],
'weekdays' => ['Chumapiri', 'Chumatato', 'Chumaine', 'Chumatano', 'Aramisi', 'Ichuma', 'Esabato'],
'weekdays_short' => ['Cpr', 'Ctt', 'Cmn', 'Cmt', 'Ars', 'Icm', 'Est'],
'weekdays_min' => ['Cpr', 'Ctt', 'Cmn', 'Cmt', 'Ars', 'Icm', 'Est'],
'months' => ['Chanuari', 'Feburari', 'Machi', 'Apiriri', 'Mei', 'Juni', 'Chulai', 'Agosti', 'Septemba', 'Okitoba', 'Nobemba', 'Disemba'],
'months_short' => ['Can', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Cul', 'Agt', 'Sep', 'Okt', 'Nob', 'Dis'],
'formats' => [
'LT' => 'HH:mm',
'LTS' => 'HH:mm:ss',
'L' => 'DD/MM/YYYY',
'LL' => 'D MMM YYYY',
'LLL' => 'D MMMM YYYY HH:mm',
'LLLL' => 'dddd, D MMMM YYYY HH:mm',
],

'month' => ':count omotunyi', 
'm' => ':count omotunyi', 
'a_month' => ':count omotunyi', 

'week' => ':count isano naibere', 
'w' => ':count isano naibere', 
'a_week' => ':count isano naibere', 

'second' => ':count ibere', 
's' => ':count ibere', 
'a_second' => ':count ibere', 

'year' => ':count omwaka',
'y' => ':count omwaka',
'a_year' => ':count omwaka',

'day' => ':count rituko',
'd' => ':count rituko',
'a_day' => ':count rituko',
]);
