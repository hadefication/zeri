<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'DD.MM.YYYY',
],
'months' => ['Qunxa Garablu', 'Kudo', 'Ciggilta Kudo', 'Agda Baxisso', 'Caxah Alsa', 'Qasa Dirri', 'Qado Dirri', 'Liiqen', 'Waysu', 'Diteli', 'Ximoli', 'Kaxxa Garablu'],
'months_short' => ['qun', 'nah', 'cig', 'agd', 'cax', 'qas', 'qad', 'leq', 'way', 'dit', 'xim', 'kax'],
'weekdays' => ['Acaada', 'Etleeni', 'Talaata', 'Arbaqa', 'Kamiisi', 'Gumqata', 'Sabti'],
'weekdays_short' => ['aca', 'etl', 'tal', 'arb', 'kam', 'gum', 'sab'],
'weekdays_min' => ['aca', 'etl', 'tal', 'arb', 'kam', 'gum', 'sab'],
'first_day_of_week' => 6,
'day_of_first_week_of_year' => 1,
'meridiem' => ['saaku', 'carra'],

'year' => ':count gaqambo', 
'y' => ':count gaqambo', 
'a_year' => ':count gaqambo', 

'month' => ':count àlsa',
'm' => ':count àlsa',
'a_month' => ':count àlsa',

'day' => ':count saaku', 
'd' => ':count saaku', 
'a_day' => ':count saaku', 

'hour' => ':count ayti', 
'h' => ':count ayti', 
'a_hour' => ':count ayti', 
]);
