<?php










return array_replace_recursive(require __DIR__.'/en.php', [
'first_day_of_week' => 0,
'meridiem' => ['ꎸꄑ', 'ꁯꋒ'],
'weekdays' => ['ꑭꆏꑍ', 'ꆏꊂꋍ', 'ꆏꊂꑍ', 'ꆏꊂꌕ', 'ꆏꊂꇖ', 'ꆏꊂꉬ', 'ꆏꊂꃘ'],
'weekdays_short' => ['ꑭꆏ', 'ꆏꋍ', 'ꆏꑍ', 'ꆏꌕ', 'ꆏꇖ', 'ꆏꉬ', 'ꆏꃘ'],
'weekdays_min' => ['ꑭꆏ', 'ꆏꋍ', 'ꆏꑍ', 'ꆏꌕ', 'ꆏꇖ', 'ꆏꉬ', 'ꆏꃘ'],
'months' => null,
'months_short' => ['ꋍꆪ', 'ꑍꆪ', 'ꌕꆪ', 'ꇖꆪ', 'ꉬꆪ', 'ꃘꆪ', 'ꏃꆪ', 'ꉆꆪ', 'ꈬꆪ', 'ꊰꆪ', 'ꊰꊪꆪ', 'ꊰꑋꆪ'],
'formats' => [
'LT' => 'h:mm a',
'LTS' => 'h:mm:ss a',
'L' => 'YYYY-MM-dd',
'LL' => 'YYYY MMM D',
'LLL' => 'YYYY MMMM D h:mm a',
'LLLL' => 'YYYY MMMM D, dddd h:mm a',
],

'year' => ':count ꒉ', 
'y' => ':count ꒉ', 
'a_year' => ':count ꒉ', 

'month' => ':count ꆪ',
'm' => ':count ꆪ',
'a_month' => ':count ꆪ',

'week' => ':count ꏃ', 
'w' => ':count ꏃ', 
'a_week' => ':count ꏃ', 

'day' => ':count ꏜ', 
'd' => ':count ꏜ', 
'a_day' => ':count ꏜ', 

'hour' => ':count ꄮꈉ',
'h' => ':count ꄮꈉ',
'a_hour' => ':count ꄮꈉ',

'minute' => ':count ꀄꊭ', 
'min' => ':count ꀄꊭ', 
'a_minute' => ':count ꀄꊭ', 

'second' => ':count ꇅ', 
's' => ':count ꇅ', 
'a_second' => ':count ꇅ', 
]);
