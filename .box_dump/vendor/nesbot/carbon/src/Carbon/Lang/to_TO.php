<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'first_day_of_week' => 0,
'formats' => [
'L' => 'dddd DD MMM YYYY',
],
'months' => ['Sānuali', 'Fēpueli', 'Maʻasi', 'ʻEpeleli', 'Mē', 'Sune', 'Siulai', 'ʻAokosi', 'Sepitema', 'ʻOkatopa', 'Nōvema', 'Tīsema'],
'months_short' => ['Sān', 'Fēp', 'Maʻa', 'ʻEpe', 'Mē', 'Sun', 'Siu', 'ʻAok', 'Sep', 'ʻOka', 'Nōv', 'Tīs'],
'weekdays' => ['Sāpate', 'Mōnite', 'Tūsite', 'Pulelulu', 'Tuʻapulelulu', 'Falaite', 'Tokonaki'],
'weekdays_short' => ['Sāp', 'Mōn', 'Tūs', 'Pul', 'Tuʻa', 'Fal', 'Tok'],
'weekdays_min' => ['Sāp', 'Mōn', 'Tūs', 'Pul', 'Tuʻa', 'Fal', 'Tok'],
'meridiem' => ['hengihengi', 'efiafi'],

'year' => ':count fitu', 
'y' => ':count fitu', 
'a_year' => ':count fitu', 

'month' => ':count mahina', 
'm' => ':count mahina', 
'a_month' => ':count mahina', 

'week' => ':count Sapate', 
'w' => ':count Sapate', 
'a_week' => ':count Sapate', 

'day' => ':count ʻaho', 
'd' => ':count ʻaho', 
'a_day' => ':count ʻaho', 

'hour' => ':count houa',
'h' => ':count houa',
'a_hour' => ':count houa',

'minute' => ':count miniti',
'min' => ':count miniti',
'a_minute' => ':count miniti',

'second' => ':count sekoni',
's' => ':count sekoni',
'a_second' => ':count sekoni',
]);
