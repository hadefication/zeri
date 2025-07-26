<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'DD/MM/YY',
],
'months' => ['jenuari', 'febuari', 'mas', 'epril', 'mei', 'jun', 'julai', 'ögus', 'septemba', 'öktoba', 'nöwemba', 'diksemba'],
'months_short' => ['jen', 'feb', 'mas', 'epr', 'mei', 'jun', 'jul', 'ögu', 'sep', 'ökt', 'nöw', 'dis'],
'weekdays' => ['sönda', 'mönda', 'sinda', 'mitiwö', 'sogipbono', 'nenggo', 'söndanggie'],
'weekdays_short' => ['sön', 'mön', 'sin', 'mit', 'soi', 'nen', 'sab'],
'weekdays_min' => ['sön', 'mön', 'sin', 'mit', 'soi', 'nen', 'sab'],
'first_day_of_week' => 0,
'day_of_first_week_of_year' => 1,
]);
