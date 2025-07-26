<?php










return array_replace_recursive(require __DIR__.'/sv.php', [
'formats' => [
'L' => 'YYYY-MM-dd',
'LL' => 'D MMM YYYY',
'LLL' => 'D MMMM YYYY HH:mm',
'LLLL' => 'dddd D MMMM YYYY HH:mm',
],
]);
