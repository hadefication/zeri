<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'DD/MM/YY',
],
'months' => ['Petsatin', 'Kupitin', 'Uyaitin', 'Tayutin', 'Kegketin', 'Tegmatin', 'Kuntutin', 'Yagkujutin', 'Daiktatin', 'Ipamtatin', 'Shinutin', 'Sakamtin'],
'months_short' => ['Pet', 'Kup', 'Uya', 'Tay', 'Keg', 'Teg', 'Kun', 'Yag', 'Dait', 'Ipam', 'Shin', 'Sak'],
'weekdays' => ['Tuntuamtin', 'Achutin', 'Kugkuktin', 'Saketin', 'Shimpitin', 'Imaptin', 'Bataetin'],
'weekdays_short' => ['Tun', 'Ach', 'Kug', 'Sak', 'Shim', 'Im', 'Bat'],
'weekdays_min' => ['Tun', 'Ach', 'Kug', 'Sak', 'Shim', 'Im', 'Bat'],
'first_day_of_week' => 0,
'day_of_first_week_of_year' => 7,
'meridiem' => ['VM', 'NM'],

'year' => ':count yaya', 
'y' => ':count yaya', 
'a_year' => ':count yaya', 

'month' => ':count nantu', 
'm' => ':count nantu', 
'a_month' => ':count nantu', 

'day' => ':count nayaim', 
'd' => ':count nayaim', 
'a_day' => ':count nayaim', 

'hour' => ':count kuwiš', 
'h' => ':count kuwiš', 
'a_hour' => ':count kuwiš', 
]);
