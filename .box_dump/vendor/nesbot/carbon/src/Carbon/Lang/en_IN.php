<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'LT' => 'HH:mm',
'LTS' => 'HH:mm:ss',
'L' => 'DD/MM/YY',
'LL' => 'MMMM DD, YYYY',
'LLL' => 'DD MMM HH:mm',
'LLLL' => 'MMMM DD, YYYY HH:mm',
],
]);
