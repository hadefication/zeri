<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'DD/MM/YYYY',
],
'months' => ['ጠሐረ', 'ከተተ', 'መገበ', 'አኀዘ', 'ግንባት', 'ሠንየ', 'ሐመለ', 'ነሐሰ', 'ከረመ', 'ጠቀመ', 'ኀደረ', 'ኀሠሠ'],
'months_short' => ['ጠሐረ', 'ከተተ', 'መገበ', 'አኀዘ', 'ግንባ', 'ሠንየ', 'ሐመለ', 'ነሐሰ', 'ከረመ', 'ጠቀመ', 'ኀደረ', 'ኀሠሠ'],
'weekdays' => ['እኁድ', 'ሰኑይ', 'ሠሉስ', 'ራብዕ', 'ሐሙስ', 'ዓርበ', 'ቀዳሚት'],
'weekdays_short' => ['እኁድ', 'ሰኑይ', 'ሠሉስ', 'ራብዕ', 'ሐሙስ', 'ዓርበ', 'ቀዳሚ'],
'weekdays_min' => ['እኁድ', 'ሰኑይ', 'ሠሉስ', 'ራብዕ', 'ሐሙስ', 'ዓርበ', 'ቀዳሚ'],
'first_day_of_week' => 1,
'day_of_first_week_of_year' => 1,
'meridiem' => ['ጽባሕ', 'ምሴት'],

'month' => ':count ወርሕ', 
'm' => ':count ወርሕ', 
'a_month' => ':count ወርሕ', 

'week' => ':count ሰብዑ', 
'w' => ':count ሰብዑ', 
'a_week' => ':count ሰብዑ', 

'hour' => ':count አንትሙ', 
'h' => ':count አንትሙ', 
'a_hour' => ':count አንትሙ', 

'minute' => ':count ንኡስ', 
'min' => ':count ንኡስ', 
'a_minute' => ':count ንኡስ', 

'year' => ':count ዓመት',
'y' => ':count ዓመት',
'a_year' => ':count ዓመት',

'day' => ':count ዕለት',
'd' => ':count ዕለት',
'a_day' => ':count ዕለት',

'second' => ':count ካልእ',
's' => ':count ካልእ',
'a_second' => ':count ካልእ',
]);
