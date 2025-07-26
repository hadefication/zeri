<?php










return array_replace_recursive(require __DIR__.'/uz.php', [
'formats' => [
'L' => 'DD/MM/yy',
'LL' => 'D MMM, YYYY',
'LLL' => 'D MMMM, YYYY HH:mm',
'LLLL' => 'dddd, DD MMMM, YYYY HH:mm',
],
'meridiem' => ['ТО', 'ТК'],
]);
