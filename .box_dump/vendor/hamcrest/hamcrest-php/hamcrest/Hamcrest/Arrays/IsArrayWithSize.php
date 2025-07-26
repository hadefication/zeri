<?php
namespace Hamcrest\Arrays;




use Hamcrest\Core\DescribedAs;
use Hamcrest\Core\IsNot;
use Hamcrest\FeatureMatcher;
use Hamcrest\Matcher;
use Hamcrest\Util;




class IsArrayWithSize extends FeatureMatcher
{

public function __construct(Matcher $sizeMatcher)
{
parent::__construct(
self::TYPE_ARRAY,
null,
$sizeMatcher,
'an array with size',
'array size'
);
}

protected function featureValueOf($array)
{
return count($array);
}

/**
@factory





*/
public static function arrayWithSize($size)
{
return new self(Util::wrapValueWithIsEqual($size));
}

/**
@factory


*/
public static function emptyArray()
{
return DescribedAs::describedAs(
'an empty array',
self::arrayWithSize(0)
);
}

/**
@factory


*/
public static function nonEmptyArray()
{
return DescribedAs::describedAs(
'a non-empty array',
self::arrayWithSize(IsNot::not(0))
);
}
}
