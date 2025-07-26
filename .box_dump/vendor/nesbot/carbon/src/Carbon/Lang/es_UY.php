<?php














return array_replace_recursive(require __DIR__.'/es.php', [
'months' => ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'setiembre', 'octubre', 'noviembre', 'diciembre'],
'months_short' => ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'set', 'oct', 'nov', 'dic'],
'first_day_of_week' => 1,
'day_of_first_week_of_year' => 1,
]);
