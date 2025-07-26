<?php










return array_replace_recursive(require __DIR__.'/fa.php', [
'meridiem' => ['ق', 'ب'],
'weekend' => [4, 5],
'formats' => [
'L' => 'OY/OM/OD',
'LL' => 'OD MMM OY',
'LLL' => 'OD MMMM OY،‏ H:mm',
'LLLL' => 'dddd OD MMMM OY،‏ H:mm',
],
]);
