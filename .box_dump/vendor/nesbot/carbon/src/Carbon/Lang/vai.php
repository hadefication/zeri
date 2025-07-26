<?php










return array_replace_recursive(require __DIR__.'/en.php', [
'weekdays' => ['ꕞꕌꔵ', 'ꗳꗡꘉ', 'ꕚꕞꕚ', 'ꕉꕞꕒ', 'ꕉꔤꕆꕢ', 'ꕉꔤꕀꕮ', 'ꔻꔬꔳ'],
'weekdays_short' => ['ꕞꕌꔵ', 'ꗳꗡꘉ', 'ꕚꕞꕚ', 'ꕉꕞꕒ', 'ꕉꔤꕆꕢ', 'ꕉꔤꕀꕮ', 'ꔻꔬꔳ'],
'weekdays_min' => ['ꕞꕌꔵ', 'ꗳꗡꘉ', 'ꕚꕞꕚ', 'ꕉꕞꕒ', 'ꕉꔤꕆꕢ', 'ꕉꔤꕀꕮ', 'ꔻꔬꔳ'],
'months' => ['ꖨꖕ ꕪꕴ ꔞꔀꕮꕊ', 'ꕒꕡꖝꖕ', 'ꕾꖺ', 'ꖢꖕ', 'ꖑꕱ', 'ꖱꘋ', 'ꖱꕞꔤ', 'ꗛꔕ', 'ꕢꕌ', 'ꕭꖃ', 'ꔞꘋꕔꕿ ꕸꖃꗏ', 'ꖨꖕ ꕪꕴ ꗏꖺꕮꕊ'],
'months_short' => ['ꖨꖕꔞ', 'ꕒꕡ', 'ꕾꖺ', 'ꖢꖕ', 'ꖑꕱ', 'ꖱꘋ', 'ꖱꕞ', 'ꗛꔕ', 'ꕢꕌ', 'ꕭꖃ', 'ꔞꘋ', 'ꖨꖕꗏ'],
'first_day_of_week' => 1,
'formats' => [
'LT' => 'h:mm a',
'LTS' => 'h:mm:ss a',
'L' => 'DD/MM/YYYY',
'LL' => 'D MMM YYYY',
'LLL' => 'D MMMM YYYY h:mm a',
'LLLL' => 'dddd, D MMMM YYYY h:mm a',
],

'year' => ':count ꕀ', 
'y' => ':count ꕀ', 
'a_year' => ':count ꕀ', 

'second' => ':count ꗱꕞꕯꕊ', 
's' => ':count ꗱꕞꕯꕊ', 
'a_second' => ':count ꗱꕞꕯꕊ', 
]);
