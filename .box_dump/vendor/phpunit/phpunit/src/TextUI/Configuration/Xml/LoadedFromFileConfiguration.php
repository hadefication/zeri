<?php declare(strict_types=1);








namespace PHPUnit\TextUI\XmlConfiguration;

use PHPUnit\TextUI\Configuration\ExtensionBootstrapCollection;
use PHPUnit\TextUI\Configuration\Php;
use PHPUnit\TextUI\Configuration\Source;
use PHPUnit\TextUI\Configuration\TestSuiteCollection;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\CodeCoverage;
use PHPUnit\TextUI\XmlConfiguration\Logging\Logging;

/**
@no-named-arguments
@immutable



*/
final readonly class LoadedFromFileConfiguration extends Configuration
{



private string $filename;
private ValidationResult $validationResult;




public function __construct(string $filename, ValidationResult $validationResult, ExtensionBootstrapCollection $extensions, Source $source, CodeCoverage $codeCoverage, Groups $groups, Logging $logging, Php $php, PHPUnit $phpunit, TestSuiteCollection $testSuite)
{
$this->filename = $filename;
$this->validationResult = $validationResult;

parent::__construct(
$extensions,
$source,
$codeCoverage,
$groups,
$logging,
$php,
$phpunit,
$testSuite,
);
}




public function filename(): string
{
return $this->filename;
}

public function hasValidationErrors(): bool
{
return $this->validationResult->hasValidationErrors();
}

public function validationErrors(): string
{
return $this->validationResult->asString();
}

public function wasLoadedFromFile(): bool
{
return true;
}
}
