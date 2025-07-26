<?php










return array_replace_recursive(require __DIR__.'/en.php', [
'meridiem' => ['ǁgoagas', 'ǃuias'],
'weekdays' => ['Sontaxtsees', 'Mantaxtsees', 'Denstaxtsees', 'Wunstaxtsees', 'Dondertaxtsees', 'Fraitaxtsees', 'Satertaxtsees'],
'weekdays_short' => ['Son', 'Ma', 'De', 'Wu', 'Do', 'Fr', 'Sat'],
'weekdays_min' => ['Son', 'Ma', 'De', 'Wu', 'Do', 'Fr', 'Sat'],
'months' => ['ǃKhanni', 'ǃKhanǀgôab', 'ǀKhuuǁkhâb', 'ǃHôaǂkhaib', 'ǃKhaitsâb', 'Gamaǀaeb', 'ǂKhoesaob', 'Aoǁkhuumûǁkhâb', 'Taraǀkhuumûǁkhâb', 'ǂNûǁnâiseb', 'ǀHooǂgaeb', 'Hôasoreǁkhâb'],
'months_short' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
'first_day_of_week' => 1,
'formats' => [
'LT' => 'h:mm a',
'LTS' => 'h:mm:ss a',
'L' => 'DD/MM/YYYY',
'LL' => 'D MMM YYYY',
'LLL' => 'D MMMM YYYY h:mm a',
'LLLL' => 'dddd, D MMMM YYYY h:mm a',
],

'year' => ':count kurigu',
'y' => ':count kurigu',
'a_year' => ':count kurigu',

'month' => ':count ǁaub', 
'm' => ':count ǁaub', 
'a_month' => ':count ǁaub', 

'week' => ':count hû', 
'w' => ':count hû', 
'a_week' => ':count hû', 

'day' => ':count ǀhobas', 
'd' => ':count ǀhobas', 
'a_day' => ':count ǀhobas', 

'hour' => ':count ǂgaes', 
'h' => ':count ǂgaes', 
'a_hour' => ':count ǂgaes', 

'minute' => ':count minutga', 
'min' => ':count minutga', 
'a_minute' => ':count minutga', 
]);
