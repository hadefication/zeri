<?php










return array_replace_recursive(require __DIR__.'/en.php', [
'first_day_of_week' => 0,
'meridiem' => ['Kiroko', 'Hwaĩ-inĩ'],
'weekdays' => ['Kiumia', 'Njumatatũ', 'Njumaine', 'Njumatana', 'Aramithi', 'Njumaa', 'Njumamothi'],
'weekdays_short' => ['KMA', 'NTT', 'NMN', 'NMT', 'ART', 'NMA', 'NMM'],
'weekdays_min' => ['KMA', 'NTT', 'NMN', 'NMT', 'ART', 'NMA', 'NMM'],
'months' => ['Njenuarĩ', 'Mwere wa kerĩ', 'Mwere wa gatatũ', 'Mwere wa kana', 'Mwere wa gatano', 'Mwere wa gatandatũ', 'Mwere wa mũgwanja', 'Mwere wa kanana', 'Mwere wa kenda', 'Mwere wa ikũmi', 'Mwere wa ikũmi na ũmwe', 'Ndithemba'],
'months_short' => ['JEN', 'WKR', 'WGT', 'WKN', 'WTN', 'WTD', 'WMJ', 'WNN', 'WKD', 'WIK', 'WMW', 'DIT'],
'formats' => [
'LT' => 'HH:mm',
'LTS' => 'HH:mm:ss',
'L' => 'DD/MM/YYYY',
'LL' => 'D MMM YYYY',
'LLL' => 'D MMMM YYYY HH:mm',
'LLLL' => 'dddd, D MMMM YYYY HH:mm',
],

'year' => ':count mĩaka', 
'y' => ':count mĩaka', 
'a_year' => ':count mĩaka', 

'month' => ':count mweri', 
'm' => ':count mweri', 
'a_month' => ':count mweri', 

'week' => ':count kiumia', 
'w' => ':count kiumia', 
'a_week' => ':count kiumia', 

'day' => ':count mũthenya', 
'd' => ':count mũthenya', 
'a_day' => ':count mũthenya', 

'hour' => ':count thaa', 
'h' => ':count thaa', 
'a_hour' => ':count thaa', 

'minute' => ':count mundu', 
'min' => ':count mundu', 
'a_minute' => ':count mundu', 

'second' => ':count igego', 
's' => ':count igego', 
'a_second' => ':count igego', 
]);
