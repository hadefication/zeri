<?php














return array_replace_recursive(require __DIR__.'/en.php', [
'formats' => [
'L' => 'OY[年]MMMMOD[日]',
],
'months' => ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
'months_short' => [' 一 ', ' 二 ', ' 三 ', ' 四 ', ' 五 ', ' 六 ', ' 七 ', ' 八 ', ' 九 ', ' 十 ', '十一', '十二'],
'weekdays' => ['週日', '週一', '週二', '週三', '週四', '週五', '週六'],
'weekdays_short' => ['日', '一', '二', '三', '四', '五', '六'],
'weekdays_min' => ['日', '一', '二', '三', '四', '五', '六'],
'first_day_of_week' => 0,
'day_of_first_week_of_year' => 1,
'alt_numbers' => ['〇', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十', '十一', '十二', '十三', '十四', '十五', '十六', '十七', '十八', '十九', '廿', '廿一', '廿二', '廿三', '廿四', '廿五', '廿六', '廿七', '廿八', '廿九', '卅', '卅一'],
'meridiem' => ['朝', '暮'],

'year' => ':count 夏', 
'y' => ':count 夏', 
'a_year' => ':count 夏', 

'month' => ':count 月', 
'm' => ':count 月', 
'a_month' => ':count 月', 

'hour' => ':count 氧', 
'h' => ':count 氧', 
'a_hour' => ':count 氧', 

'minute' => ':count 點', 
'min' => ':count 點', 
'a_minute' => ':count 點', 

'second' => ':count 楚', 
's' => ':count 楚', 
'a_second' => ':count 楚', 

'week' => ':count 星期',
'w' => ':count 星期',
'a_week' => ':count 星期',

'day' => ':count 日(曆法)',
'd' => ':count 日(曆法)',
'a_day' => ':count 日(曆法)',
]);
