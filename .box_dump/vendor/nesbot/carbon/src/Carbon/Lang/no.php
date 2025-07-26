<?php

















return array_replace_recursive(require __DIR__.'/nb.php', [
'formats' => [
'LLL' => 'D. MMMM YYYY HH:mm',
'LLLL' => 'dddd, D. MMMM YYYY [kl.] HH:mm',
],
'calendar' => [
'nextWeek' => 'på dddd [kl.] LT',
'lastWeek' => '[i] dddd[s kl.] LT',
],
]);
