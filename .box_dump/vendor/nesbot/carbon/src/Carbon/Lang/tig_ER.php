<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'DD/MM/YYYY',
],
'months' => ['ጥሪ', 'ለካቲት', 'መጋቢት', 'ሚያዝያ', 'ግንቦት', 'ሰነ', 'ሓምለ', 'ነሓሰ', 'መስከረም', 'ጥቅምቲ', 'ሕዳር', 'ታሕሳስ'],
'months_short' => ['ጥሪ ', 'ለካቲ', 'መጋቢ', 'ሚያዝ', 'ግንቦ', 'ሰነ ', 'ሓምለ', 'ነሓሰ', 'መስከ', 'ጥቅም', 'ሕዳር', 'ታሕሳ'],
'weekdays' => ['ሰንበት ዓባይ', 'ሰኖ', 'ታላሸኖ', 'ኣረርባዓ', 'ከሚሽ', 'ጅምዓት', 'ሰንበት ንኢሽ'],
'weekdays_short' => ['ሰ//ዓ', 'ሰኖ ', 'ታላሸ', 'ኣረር', 'ከሚሽ', 'ጅምዓ', 'ሰ//ን'],
'weekdays_min' => ['ሰ//ዓ', 'ሰኖ ', 'ታላሸ', 'ኣረር', 'ከሚሽ', 'ጅምዓ', 'ሰ//ን'],
'first_day_of_week' => 1,
'day_of_first_week_of_year' => 1,
'meridiem' => ['ቀደም ሰር ምዕል', 'ሓቆ ሰር ምዕል'],

'year' => ':count ማይ', 
'y' => ':count ማይ', 
'a_year' => ':count ማይ', 

'month' => ':count ሸምሽ', 
'm' => ':count ሸምሽ', 
'a_month' => ':count ሸምሽ', 

'week' => ':count ሰቡዕ', 
'w' => ':count ሰቡዕ', 
'a_week' => ':count ሰቡዕ', 

'day' => ':count ዎሮ', 
'd' => ':count ዎሮ', 
'a_day' => ':count ዎሮ', 

'hour' => ':count ሰዓት', 
'h' => ':count ሰዓት', 
'a_hour' => ':count ሰዓት', 

'minute' => ':count ካልኣይት', 
'min' => ':count ካልኣይት', 
'a_minute' => ':count ካልኣይት', 

'second' => ':count ካልኣይ',
's' => ':count ካልኣይ',
'a_second' => ':count ካልኣይ',
]);
