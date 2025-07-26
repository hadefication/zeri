<?php declare(strict_types=1);








namespace PHPUnit\Metadata;

/**
@immutable
@no-named-arguments

*/
final readonly class RequiresSetting extends Metadata
{



private string $setting;




private string $value;






protected function __construct(int $level, string $setting, string $value)
{
parent::__construct($level);

$this->setting = $setting;
$this->value = $value;
}

public function isRequiresSetting(): true
{
return true;
}




public function setting(): string
{
return $this->setting;
}




public function value(): string
{
return $this->value;
}
}
