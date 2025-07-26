<?php










return array_replace_recursive(require __DIR__.'/nb.php', [
'formats' => [
'LL' => 'D. MMM YYYY',
'LLL' => 'D. MMMM YYYY, HH:mm',
'LLLL' => 'dddd D. MMMM YYYY, HH:mm',
],
]);
