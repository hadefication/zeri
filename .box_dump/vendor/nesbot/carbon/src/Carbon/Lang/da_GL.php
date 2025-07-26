<?php










return array_replace_recursive(require __DIR__.'/da.php', [
'formats' => [
'L' => 'DD/MM/YYYY',
'LL' => 'D. MMM YYYY',
'LLL' => 'D. MMMM YYYY HH.mm',
'LLLL' => 'dddd [den] D. MMMM YYYY HH.mm',
],
]);
