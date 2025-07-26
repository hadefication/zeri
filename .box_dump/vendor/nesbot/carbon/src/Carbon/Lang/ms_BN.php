<?php










return array_replace_recursive(require __DIR__.'/ms.php', [
'formats' => [
'LT' => 'h:mm a',
'LTS' => 'h:mm:ss a',
'L' => 'D/MM/yy',
'LL' => 'D MMM YYYY',
'LLL' => 'D MMMM YYYY, h:mm a',
'LLLL' => 'dd MMMM YYYY, h:mm a',
],
'meridiem' => ['a', 'p'],
]);
