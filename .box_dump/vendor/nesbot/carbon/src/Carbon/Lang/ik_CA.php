<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'DD/MM/YY',
],
'months' => ['Siqiññaatchiaq', 'Siqiññaasrugruk', 'Paniqsiqsiivik', 'Qilġich Tatqiat', 'Suppivik', 'Iġñivik', 'Itchavik', 'Tiññivik', 'Amiġaiqsivik', 'Sikkuvik', 'Nippivik', 'Siqiñġiḷaq'],
'months_short' => ['Sñt', 'Sñs', 'Pan', 'Qil', 'Sup', 'Iġñ', 'Itc', 'Tiñ', 'Ami', 'Sik', 'Nip', 'Siq'],
'weekdays' => ['Minġuiqsioiq', 'Savałłiq', 'Ilaqtchiioiq', 'Qitchiioiq', 'Sisamiioiq', 'Tallimmiioiq', 'Maqinġuoiq'],
'weekdays_short' => ['Min', 'Sav', 'Ila', 'Qit', 'Sis', 'Tal', 'Maq'],
'weekdays_min' => ['Min', 'Sav', 'Ila', 'Qit', 'Sis', 'Tal', 'Maq'],
'first_day_of_week' => 0,
'day_of_first_week_of_year' => 1,

'year' => ':count ukiuq',
'y' => ':count ukiuq',
'a_year' => ':count ukiuq',

'month' => ':count Tatqiat',
'm' => ':count Tatqiat',
'a_month' => ':count Tatqiat',

'week' => ':count tatqiat', 
'w' => ':count tatqiat', 
'a_week' => ':count tatqiat', 

'day' => ':count siqiñiq', 
'd' => ':count siqiñiq', 
'a_day' => ':count siqiñiq', 

'hour' => ':count Siḷa', 
'h' => ':count Siḷa', 
'a_hour' => ':count Siḷa', 

'second' => ':count iġñiq', 
's' => ':count iġñiq', 
'a_second' => ':count iġñiq', 
]);
