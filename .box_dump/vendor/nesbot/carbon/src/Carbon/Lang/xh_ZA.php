<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'DD/MM/YYYY',
],
'months' => ['eyoMqungu', 'eyoMdumba', 'eyoKwindla', 'uTshazimpuzi', 'uCanzibe', 'eyeSilimela', 'eyeKhala', 'eyeThupa', 'eyoMsintsi', 'eyeDwarha', 'eyeNkanga', 'eyoMnga'],
'months_short' => ['Mqu', 'Mdu', 'Kwi', 'Tsh', 'Can', 'Sil', 'Kha', 'Thu', 'Msi', 'Dwa', 'Nka', 'Mng'],
'weekdays' => ['iCawa', 'uMvulo', 'lwesiBini', 'lwesiThathu', 'ulweSine', 'lwesiHlanu', 'uMgqibelo'],
'weekdays_short' => ['Caw', 'Mvu', 'Bin', 'Tha', 'Sin', 'Hla', 'Mgq'],
'weekdays_min' => ['Caw', 'Mvu', 'Bin', 'Tha', 'Sin', 'Hla', 'Mgq'],
'first_day_of_week' => 0,
'day_of_first_week_of_year' => 1,

'year' => ':count ihlobo', 
'y' => ':count ihlobo', 
'a_year' => ':count ihlobo', 

'hour' => ':count iwotshi', 
'h' => ':count iwotshi', 
'a_hour' => ':count iwotshi', 

'minute' => ':count ingqalelo', 
'min' => ':count ingqalelo', 
'a_minute' => ':count ingqalelo', 

'second' => ':count nceda', 
's' => ':count nceda', 
'a_second' => ':count nceda', 

'month' => ':count inyanga',
'm' => ':count inyanga',
'a_month' => ':count inyanga',

'week' => ':count veki',
'w' => ':count veki',
'a_week' => ':count veki',

'day' => ':count imini',
'd' => ':count imini',
'a_day' => ':count imini',
]);
