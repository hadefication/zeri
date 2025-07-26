<?php










return array_replace_recursive(require __DIR__.'/fr.php', [
'first_day_of_week' => 6,
'weekend' => [5, 6],
'formats' => [
'LT' => 'h:mm a',
'LTS' => 'h:mm:ss a',
'L' => 'DD/MM/YYYY',
'LL' => 'D MMM YYYY',
'LLL' => 'D MMMM YYYY h:mm a',
'LLLL' => 'dddd D MMMM YYYY h:mm a',
],
]);
