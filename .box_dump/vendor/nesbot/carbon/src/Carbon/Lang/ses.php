<?php










return array_replace_recursive(require __DIR__.'/en.php', [
'meridiem' => ['Adduha', 'Aluula'],
'weekdays' => ['Alhadi', 'Atinni', 'Atalaata', 'Alarba', 'Alhamiisa', 'Alzuma', 'Asibti'],
'weekdays_short' => ['Alh', 'Ati', 'Ata', 'Ala', 'Alm', 'Alz', 'Asi'],
'weekdays_min' => ['Alh', 'Ati', 'Ata', 'Ala', 'Alm', 'Alz', 'Asi'],
'months' => ['Žanwiye', 'Feewiriye', 'Marsi', 'Awiril', 'Me', 'Žuweŋ', 'Žuyye', 'Ut', 'Sektanbur', 'Oktoobur', 'Noowanbur', 'Deesanbur'],
'months_short' => ['Žan', 'Fee', 'Mar', 'Awi', 'Me', 'Žuw', 'Žuy', 'Ut', 'Sek', 'Okt', 'Noo', 'Dee'],
'first_day_of_week' => 1,
'formats' => [
'LT' => 'HH:mm',
'LTS' => 'HH:mm:ss',
'L' => 'D/M/YYYY',
'LL' => 'D MMM YYYY',
'LLL' => 'D MMMM YYYY HH:mm',
'LLLL' => 'dddd D MMMM YYYY HH:mm',
],

'month' => ':count alaada', 
'm' => ':count alaada', 
'a_month' => ':count alaada', 

'hour' => ':count ɲaajin', 
'h' => ':count ɲaajin', 
'a_hour' => ':count ɲaajin', 

'minute' => ':count zarbu', 
'min' => ':count zarbu', 
'a_minute' => ':count zarbu', 

'year' => ':count jiiri',
'y' => ':count jiiri',
'a_year' => ':count jiiri',

'week' => ':count jirbiiyye',
'w' => ':count jirbiiyye',
'a_week' => ':count jirbiiyye',

'day' => ':count zaari',
'd' => ':count zaari',
'a_day' => ':count zaari',

'second' => ':count ihinkante',
's' => ':count ihinkante',
'a_second' => ':count ihinkante',
]);
