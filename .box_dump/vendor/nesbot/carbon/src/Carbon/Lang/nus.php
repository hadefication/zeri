<?php










return array_replace_recursive(require __DIR__.'/en.php', [
'meridiem' => ['RW', 'TŊ'],
'weekdays' => ['Cäŋ kuɔth', 'Jiec la̱t', 'Rɛw lätni', 'Diɔ̱k lätni', 'Ŋuaan lätni', 'Dhieec lätni', 'Bäkɛl lätni'],
'weekdays_short' => ['Cäŋ', 'Jiec', 'Rɛw', 'Diɔ̱k', 'Ŋuaan', 'Dhieec', 'Bäkɛl'],
'weekdays_min' => ['Cäŋ', 'Jiec', 'Rɛw', 'Diɔ̱k', 'Ŋuaan', 'Dhieec', 'Bäkɛl'],
'months' => ['Tiop thar pɛt', 'Pɛt', 'Duɔ̱ɔ̱ŋ', 'Guak', 'Duät', 'Kornyoot', 'Pay yie̱tni', 'Tho̱o̱r', 'Tɛɛr', 'Laath', 'Kur', 'Tio̱p in di̱i̱t'],
'months_short' => ['Tiop', 'Pɛt', 'Duɔ̱ɔ̱', 'Guak', 'Duä', 'Kor', 'Pay', 'Thoo', 'Tɛɛ', 'Laa', 'Kur', 'Tid'],
'first_day_of_week' => 1,
'formats' => [
'LT' => 'h:mm a',
'LTS' => 'h:mm:ss a',
'L' => 'D/MM/YYYY',
'LL' => 'D MMM YYYY',
'LLL' => 'D MMMM YYYY h:mm a',
'LLLL' => 'dddd D MMMM YYYY h:mm a',
],

'year' => ':count jiök', 
'y' => ':count jiök', 
'a_year' => ':count jiök', 

'month' => ':count pay', 
'm' => ':count pay', 
'a_month' => ':count pay', 
]);
