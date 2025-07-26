<?php
namespace Hamcrest\Collection;




use Hamcrest\FeatureMatcher;
use Hamcrest\Matcher;
use Hamcrest\Util;




class IsTraversableWithSize extends FeatureMatcher
{

public function __construct(Matcher $sizeMatcher)
{
parent::__construct(
self::TYPE_OBJECT,
'Traversable',
$sizeMatcher,
'a traversable with size',
'traversable size'
);
}

protected function featureValueOf($actual)
{
$size = 0;
foreach ($actual as $value) {
$size++;
}

return $size;
}

/**
@factory


*/
public static function traversableWithSize($size)
{
return new self(Util::wrapValueWithIsEqual($size));
}
}
