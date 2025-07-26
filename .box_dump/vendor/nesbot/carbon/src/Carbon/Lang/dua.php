<?php










return array_replace_recursive(require __DIR__.'/en.php', [
'meridiem' => ['idiɓa', 'ebyámu'],
'weekdays' => ['éti', 'mɔ́sú', 'kwasú', 'mukɔ́sú', 'ŋgisú', 'ɗónɛsú', 'esaɓasú'],
'weekdays_short' => ['ét', 'mɔ́s', 'kwa', 'muk', 'ŋgi', 'ɗón', 'esa'],
'weekdays_min' => ['ét', 'mɔ́s', 'kwa', 'muk', 'ŋgi', 'ɗón', 'esa'],
'months' => ['dimɔ́di', 'ŋgɔndɛ', 'sɔŋɛ', 'diɓáɓá', 'emiasele', 'esɔpɛsɔpɛ', 'madiɓɛ́díɓɛ́', 'diŋgindi', 'nyɛtɛki', 'mayésɛ́', 'tiníní', 'eláŋgɛ́'],
'months_short' => ['di', 'ŋgɔn', 'sɔŋ', 'diɓ', 'emi', 'esɔ', 'mad', 'diŋ', 'nyɛt', 'may', 'tin', 'elá'],
'first_day_of_week' => 1,
'formats' => [
'LT' => 'HH:mm',
'LTS' => 'HH:mm:ss',
'L' => 'D/M/YYYY',
'LL' => 'D MMM YYYY',
'LLL' => 'D MMMM YYYY HH:mm',
'LLLL' => 'dddd D MMMM YYYY HH:mm',
],

'year' => ':count ma mbu', 
'y' => ':count ma mbu', 
'a_year' => ':count ma mbu', 

'month' => ':count myo̱di', 
'm' => ':count myo̱di', 
'a_month' => ':count myo̱di', 

'week' => ':count woki', 
'w' => ':count woki', 
'a_week' => ':count woki', 

'day' => ':count buńa', 
'd' => ':count buńa', 
'a_day' => ':count buńa', 

'hour' => ':count ma awa', 
'h' => ':count ma awa', 
'a_hour' => ':count ma awa', 

'minute' => ':count minuti', 
'min' => ':count minuti', 
'a_minute' => ':count minuti', 

'second' => ':count maba', 
's' => ':count maba', 
'a_second' => ':count maba', 
]);
