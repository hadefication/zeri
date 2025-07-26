<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'DD/MM/YYYY',
],
'months' => ['Pherekgong', 'Hlakola', 'Tlhakubele', 'Mmese', 'Motsheanong', 'Phupjane', 'Phupu', 'Phato', 'Leotse', 'Mphalane', 'Pudungwana', 'Tshitwe'],
'months_short' => ['Phe', 'Hla', 'TlH', 'Mme', 'Mot', 'Jan', 'Upu', 'Pha', 'Leo', 'Mph', 'Pud', 'Tsh'],
'weekdays' => ['Sontaha', 'Mantaha', 'Labobedi', 'Laboraro', 'Labone', 'Labohlano', 'Moqebelo'],
'weekdays_short' => ['Son', 'Mma', 'Bed', 'Rar', 'Ne', 'Hla', 'Moq'],
'weekdays_min' => ['Son', 'Mma', 'Bed', 'Rar', 'Ne', 'Hla', 'Moq'],
'first_day_of_week' => 0,
'day_of_first_week_of_year' => 1,

'week' => ':count Sontaha', 
'w' => ':count Sontaha', 
'a_week' => ':count Sontaha', 

'day' => ':count letsatsi', 
'd' => ':count letsatsi', 
'a_day' => ':count letsatsi', 

'hour' => ':count sešupanako', 
'h' => ':count sešupanako', 
'a_hour' => ':count sešupanako', 

'minute' => ':count menyane', 
'min' => ':count menyane', 
'a_minute' => ':count menyane', 

'second' => ':count thusa', 
's' => ':count thusa', 
'a_second' => ':count thusa', 

'year' => ':count selemo',
'y' => ':count selemo',
'a_year' => ':count selemo',

'month' => ':count kgwedi',
'm' => ':count kgwedi',
'a_month' => ':count kgwedi',
]);
