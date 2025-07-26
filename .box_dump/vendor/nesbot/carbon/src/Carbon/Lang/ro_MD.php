<?php










return array_replace_recursive(require __DIR__.'/ro.php', [
'formats' => [
'LT' => 'HH:mm',
'LTS' => 'HH:mm:ss',
'L' => 'DD.MM.YYYY',
'LL' => 'D MMM YYYY',
'LLL' => 'D MMMM YYYY, HH:mm',
'LLLL' => 'dddd, D MMMM YYYY, HH:mm',
],
]);
