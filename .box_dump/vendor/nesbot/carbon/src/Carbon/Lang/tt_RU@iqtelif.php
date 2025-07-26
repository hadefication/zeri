<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'DD.MM.YYYY',
],
'months' => ['Ğınwar', 'Fiwral\'', 'Mart', 'April', 'May', 'Yün', 'Yül', 'Awgust', 'Sintebír', 'Üktebír', 'Noyebír', 'Dikebír'],
'months_short' => ['Ğın', 'Fiw', 'Mar', 'Apr', 'May', 'Yün', 'Yül', 'Awg', 'Sin', 'Ükt', 'Noy', 'Dik'],
'weekdays' => ['Yekşembí', 'Düşembí', 'Sişembí', 'Çerşembí', 'Pencíşembí', 'Comğa', 'Şimbe'],
'weekdays_short' => ['Yek', 'Düş', 'Siş', 'Çer', 'Pen', 'Com', 'Şim'],
'weekdays_min' => ['Yek', 'Düş', 'Siş', 'Çer', 'Pen', 'Com', 'Şim'],
'first_day_of_week' => 1,
'day_of_first_week_of_year' => 1,
'meridiem' => ['ÖA', 'ÖS'],
]);
