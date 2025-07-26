<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'YYYY-MM-DD',
],
'months' => ['Jenner', 'Hornig', 'Märze', 'Abrille', 'Meije', 'Bráčet', 'Heiwet', 'Öigšte', 'Herbštmánet', 'Wímánet', 'Wintermánet', 'Chrištmánet'],
'months_short' => ['Jen', 'Hor', 'Mär', 'Abr', 'Mei', 'Brá', 'Hei', 'Öig', 'Her', 'Wím', 'Win', 'Chr'],
'weekdays' => ['Suntag', 'Mäntag', 'Zischtag', 'Mittwuch', 'Frontag', 'Fritag', 'Samschtag'],
'weekdays_short' => ['Sun', 'Män', 'Zis', 'Mit', 'Fro', 'Fri', 'Sam'],
'weekdays_min' => ['Sun', 'Män', 'Zis', 'Mit', 'Fro', 'Fri', 'Sam'],
'first_day_of_week' => 1,
'day_of_first_week_of_year' => 4,

'month' => ':count Maano', 
'm' => ':count Maano', 
'a_month' => ':count Maano', 
]);
