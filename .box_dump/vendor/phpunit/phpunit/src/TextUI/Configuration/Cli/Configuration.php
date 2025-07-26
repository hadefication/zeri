<?php declare(strict_types=1);








namespace PHPUnit\TextUI\CliArguments;

/**
@no-named-arguments
@immutable



*/
final readonly class Configuration
{



private array $arguments;
private ?string $atLeastVersion;
private ?bool $backupGlobals;
private ?bool $backupStaticProperties;
private ?bool $beStrictAboutChangesToGlobalState;
private ?string $bootstrap;
private ?string $cacheDirectory;
private ?bool $cacheResult;
private bool $checkVersion;
private ?string $colors;
private null|int|string $columns;
private ?string $configurationFile;




private ?array $coverageFilter;
private ?string $coverageClover;
private ?string $coverageCobertura;
private ?string $coverageCrap4J;
private ?string $coverageHtml;
private ?string $coveragePhp;
private ?string $coverageText;
private ?bool $coverageTextShowUncoveredFiles;
private ?bool $coverageTextShowOnlySummary;
private ?string $coverageXml;
private ?bool $pathCoverage;
private bool $warmCoverageCache;
private ?int $defaultTimeLimit;
private ?bool $disableCodeCoverageIgnore;
private ?bool $disallowTestOutput;
private ?bool $enforceTimeLimit;




private ?array $excludeGroups;
private ?int $executionOrder;
private ?int $executionOrderDefects;
private ?bool $failOnDeprecation;
private ?bool $failOnPhpunitDeprecation;
private ?bool $failOnEmptyTestSuite;
private ?bool $failOnIncomplete;
private ?bool $failOnNotice;
private ?bool $failOnRisky;
private ?bool $failOnSkipped;
private ?bool $failOnWarning;
private ?bool $stopOnDefect;
private ?bool $stopOnDeprecation;
private ?string $specificDeprecationToStopOn;
private ?bool $stopOnError;
private ?bool $stopOnFailure;
private ?bool $stopOnIncomplete;
private ?bool $stopOnNotice;
private ?bool $stopOnRisky;
private ?bool $stopOnSkipped;
private ?bool $stopOnWarning;
private ?string $filter;
private ?string $excludeFilter;
private ?string $generateBaseline;
private ?string $useBaseline;
private bool $ignoreBaseline;
private bool $generateConfiguration;
private bool $migrateConfiguration;




private ?array $groups;




private ?array $testsCovering;




private ?array $testsUsing;




private ?array $testsRequiringPhpExtension;
private bool $help;
private ?string $includePath;




private ?array $iniSettings;
private ?string $junitLogfile;
private bool $listGroups;
private bool $listSuites;
private bool $listTestFiles;
private bool $listTests;
private ?string $listTestsXml;
private ?bool $noCoverage;
private ?bool $noExtensions;
private ?bool $noOutput;
private ?bool $noProgress;
private ?bool $noResults;
private ?bool $noLogging;
private ?bool $processIsolation;
private ?int $randomOrderSeed;
private ?bool $reportUselessTests;
private ?bool $resolveDependencies;
private ?bool $reverseList;
private ?bool $stderr;
private ?bool $strictCoverage;
private ?string $teamcityLogfile;
private ?bool $teamCityPrinter;
private ?string $testdoxHtmlFile;
private ?string $testdoxTextFile;
private ?bool $testdoxPrinter;
private ?bool $testdoxPrinterSummary;




private ?array $testSuffixes;
private ?string $testSuite;
private ?string $excludeTestSuite;
private bool $useDefaultConfiguration;
private ?bool $displayDetailsOnIncompleteTests;
private ?bool $displayDetailsOnSkippedTests;
private ?bool $displayDetailsOnTestsThatTriggerDeprecations;
private ?bool $displayDetailsOnPhpunitDeprecations;
private ?bool $displayDetailsOnTestsThatTriggerErrors;
private ?bool $displayDetailsOnTestsThatTriggerNotices;
private ?bool $displayDetailsOnTestsThatTriggerWarnings;
private bool $version;
private ?string $logEventsText;
private ?string $logEventsVerboseText;
private bool $debug;




private ?array $extensions;













public function __construct(array $arguments, ?string $atLeastVersion, ?bool $backupGlobals, ?bool $backupStaticProperties, ?bool $beStrictAboutChangesToGlobalState, ?string $bootstrap, ?string $cacheDirectory, ?bool $cacheResult, bool $checkVersion, ?string $colors, null|int|string $columns, ?string $configurationFile, ?string $coverageClover, ?string $coverageCobertura, ?string $coverageCrap4J, ?string $coverageHtml, ?string $coveragePhp, ?string $coverageText, ?bool $coverageTextShowUncoveredFiles, ?bool $coverageTextShowOnlySummary, ?string $coverageXml, ?bool $pathCoverage, bool $warmCoverageCache, ?int $defaultTimeLimit, ?bool $disableCodeCoverageIgnore, ?bool $disallowTestOutput, ?bool $enforceTimeLimit, ?array $excludeGroups, ?int $executionOrder, ?int $executionOrderDefects, ?bool $failOnDeprecation, ?bool $failOnPhpunitDeprecation, ?bool $failOnEmptyTestSuite, ?bool $failOnIncomplete, ?bool $failOnNotice, ?bool $failOnRisky, ?bool $failOnSkipped, ?bool $failOnWarning, ?bool $stopOnDefect, ?bool $stopOnDeprecation, ?string $specificDeprecationToStopOn, ?bool $stopOnError, ?bool $stopOnFailure, ?bool $stopOnIncomplete, ?bool $stopOnNotice, ?bool $stopOnRisky, ?bool $stopOnSkipped, ?bool $stopOnWarning, ?string $filter, ?string $excludeFilter, ?string $generateBaseline, ?string $useBaseline, bool $ignoreBaseline, bool $generateConfiguration, bool $migrateConfiguration, ?array $groups, ?array $testsCovering, ?array $testsUsing, ?array $testsRequiringPhpExtension, bool $help, ?string $includePath, ?array $iniSettings, ?string $junitLogfile, bool $listGroups, bool $listSuites, bool $listTestFiles, bool $listTests, ?string $listTestsXml, ?bool $noCoverage, ?bool $noExtensions, ?bool $noOutput, ?bool $noProgress, ?bool $noResults, ?bool $noLogging, ?bool $processIsolation, ?int $randomOrderSeed, ?bool $reportUselessTests, ?bool $resolveDependencies, ?bool $reverseList, ?bool $stderr, ?bool $strictCoverage, ?string $teamcityLogfile, ?string $testdoxHtmlFile, ?string $testdoxTextFile, ?array $testSuffixes, ?string $testSuite, ?string $excludeTestSuite, bool $useDefaultConfiguration, ?bool $displayDetailsOnIncompleteTests, ?bool $displayDetailsOnSkippedTests, ?bool $displayDetailsOnTestsThatTriggerDeprecations, ?bool $displayDetailsOnPhpunitDeprecations, ?bool $displayDetailsOnTestsThatTriggerErrors, ?bool $displayDetailsOnTestsThatTriggerNotices, ?bool $displayDetailsOnTestsThatTriggerWarnings, bool $version, ?array $coverageFilter, ?string $logEventsText, ?string $logEventsVerboseText, ?bool $printerTeamCity, ?bool $testdoxPrinter, ?bool $testdoxPrinterSummary, bool $debug, ?array $extensions)
{
$this->arguments = $arguments;
$this->atLeastVersion = $atLeastVersion;
$this->backupGlobals = $backupGlobals;
$this->backupStaticProperties = $backupStaticProperties;
$this->beStrictAboutChangesToGlobalState = $beStrictAboutChangesToGlobalState;
$this->bootstrap = $bootstrap;
$this->cacheDirectory = $cacheDirectory;
$this->cacheResult = $cacheResult;
$this->checkVersion = $checkVersion;
$this->colors = $colors;
$this->columns = $columns;
$this->configurationFile = $configurationFile;
$this->coverageFilter = $coverageFilter;
$this->coverageClover = $coverageClover;
$this->coverageCobertura = $coverageCobertura;
$this->coverageCrap4J = $coverageCrap4J;
$this->coverageHtml = $coverageHtml;
$this->coveragePhp = $coveragePhp;
$this->coverageText = $coverageText;
$this->coverageTextShowUncoveredFiles = $coverageTextShowUncoveredFiles;
$this->coverageTextShowOnlySummary = $coverageTextShowOnlySummary;
$this->coverageXml = $coverageXml;
$this->pathCoverage = $pathCoverage;
$this->warmCoverageCache = $warmCoverageCache;
$this->defaultTimeLimit = $defaultTimeLimit;
$this->disableCodeCoverageIgnore = $disableCodeCoverageIgnore;
$this->disallowTestOutput = $disallowTestOutput;
$this->enforceTimeLimit = $enforceTimeLimit;
$this->excludeGroups = $excludeGroups;
$this->executionOrder = $executionOrder;
$this->executionOrderDefects = $executionOrderDefects;
$this->failOnDeprecation = $failOnDeprecation;
$this->failOnPhpunitDeprecation = $failOnPhpunitDeprecation;
$this->failOnEmptyTestSuite = $failOnEmptyTestSuite;
$this->failOnIncomplete = $failOnIncomplete;
$this->failOnNotice = $failOnNotice;
$this->failOnRisky = $failOnRisky;
$this->failOnSkipped = $failOnSkipped;
$this->failOnWarning = $failOnWarning;
$this->stopOnDefect = $stopOnDefect;
$this->stopOnDeprecation = $stopOnDeprecation;
$this->specificDeprecationToStopOn = $specificDeprecationToStopOn;
$this->stopOnError = $stopOnError;
$this->stopOnFailure = $stopOnFailure;
$this->stopOnIncomplete = $stopOnIncomplete;
$this->stopOnNotice = $stopOnNotice;
$this->stopOnRisky = $stopOnRisky;
$this->stopOnSkipped = $stopOnSkipped;
$this->stopOnWarning = $stopOnWarning;
$this->filter = $filter;
$this->excludeFilter = $excludeFilter;
$this->generateBaseline = $generateBaseline;
$this->useBaseline = $useBaseline;
$this->ignoreBaseline = $ignoreBaseline;
$this->generateConfiguration = $generateConfiguration;
$this->migrateConfiguration = $migrateConfiguration;
$this->groups = $groups;
$this->testsCovering = $testsCovering;
$this->testsUsing = $testsUsing;
$this->testsRequiringPhpExtension = $testsRequiringPhpExtension;
$this->help = $help;
$this->includePath = $includePath;
$this->iniSettings = $iniSettings;
$this->junitLogfile = $junitLogfile;
$this->listGroups = $listGroups;
$this->listSuites = $listSuites;
$this->listTestFiles = $listTestFiles;
$this->listTests = $listTests;
$this->listTestsXml = $listTestsXml;
$this->noCoverage = $noCoverage;
$this->noExtensions = $noExtensions;
$this->noOutput = $noOutput;
$this->noProgress = $noProgress;
$this->noResults = $noResults;
$this->noLogging = $noLogging;
$this->processIsolation = $processIsolation;
$this->randomOrderSeed = $randomOrderSeed;
$this->reportUselessTests = $reportUselessTests;
$this->resolveDependencies = $resolveDependencies;
$this->reverseList = $reverseList;
$this->stderr = $stderr;
$this->strictCoverage = $strictCoverage;
$this->teamcityLogfile = $teamcityLogfile;
$this->testdoxHtmlFile = $testdoxHtmlFile;
$this->testdoxTextFile = $testdoxTextFile;
$this->testSuffixes = $testSuffixes;
$this->testSuite = $testSuite;
$this->excludeTestSuite = $excludeTestSuite;
$this->useDefaultConfiguration = $useDefaultConfiguration;
$this->displayDetailsOnIncompleteTests = $displayDetailsOnIncompleteTests;
$this->displayDetailsOnSkippedTests = $displayDetailsOnSkippedTests;
$this->displayDetailsOnTestsThatTriggerDeprecations = $displayDetailsOnTestsThatTriggerDeprecations;
$this->displayDetailsOnPhpunitDeprecations = $displayDetailsOnPhpunitDeprecations;
$this->displayDetailsOnTestsThatTriggerErrors = $displayDetailsOnTestsThatTriggerErrors;
$this->displayDetailsOnTestsThatTriggerNotices = $displayDetailsOnTestsThatTriggerNotices;
$this->displayDetailsOnTestsThatTriggerWarnings = $displayDetailsOnTestsThatTriggerWarnings;
$this->version = $version;
$this->logEventsText = $logEventsText;
$this->logEventsVerboseText = $logEventsVerboseText;
$this->teamCityPrinter = $printerTeamCity;
$this->testdoxPrinter = $testdoxPrinter;
$this->testdoxPrinterSummary = $testdoxPrinterSummary;
$this->debug = $debug;
$this->extensions = $extensions;
}




public function arguments(): array
{
return $this->arguments;
}

/**
@phpstan-assert-if-true
*/
public function hasAtLeastVersion(): bool
{
return $this->atLeastVersion !== null;
}




public function atLeastVersion(): string
{
if (!$this->hasAtLeastVersion()) {
throw new Exception;
}

return $this->atLeastVersion;
}

/**
@phpstan-assert-if-true
*/
public function hasBackupGlobals(): bool
{
return $this->backupGlobals !== null;
}




public function backupGlobals(): bool
{
if (!$this->hasBackupGlobals()) {
throw new Exception;
}

return $this->backupGlobals;
}

/**
@phpstan-assert-if-true
*/
public function hasBackupStaticProperties(): bool
{
return $this->backupStaticProperties !== null;
}




public function backupStaticProperties(): bool
{
if (!$this->hasBackupStaticProperties()) {
throw new Exception;
}

return $this->backupStaticProperties;
}

/**
@phpstan-assert-if-true
*/
public function hasBeStrictAboutChangesToGlobalState(): bool
{
return $this->beStrictAboutChangesToGlobalState !== null;
}




public function beStrictAboutChangesToGlobalState(): bool
{
if (!$this->hasBeStrictAboutChangesToGlobalState()) {
throw new Exception;
}

return $this->beStrictAboutChangesToGlobalState;
}

/**
@phpstan-assert-if-true
*/
public function hasBootstrap(): bool
{
return $this->bootstrap !== null;
}




public function bootstrap(): string
{
if (!$this->hasBootstrap()) {
throw new Exception;
}

return $this->bootstrap;
}

/**
@phpstan-assert-if-true
*/
public function hasCacheDirectory(): bool
{
return $this->cacheDirectory !== null;
}




public function cacheDirectory(): string
{
if (!$this->hasCacheDirectory()) {
throw new Exception;
}

return $this->cacheDirectory;
}

/**
@phpstan-assert-if-true
*/
public function hasCacheResult(): bool
{
return $this->cacheResult !== null;
}




public function cacheResult(): bool
{
if (!$this->hasCacheResult()) {
throw new Exception;
}

return $this->cacheResult;
}

public function checkVersion(): bool
{
return $this->checkVersion;
}

/**
@phpstan-assert-if-true
*/
public function hasColors(): bool
{
return $this->colors !== null;
}




public function colors(): string
{
if (!$this->hasColors()) {
throw new Exception;
}

return $this->colors;
}

/**
@phpstan-assert-if-true
*/
public function hasColumns(): bool
{
return $this->columns !== null;
}




public function columns(): int|string
{
if (!$this->hasColumns()) {
throw new Exception;
}

return $this->columns;
}

/**
@phpstan-assert-if-true
*/
public function hasConfigurationFile(): bool
{
return $this->configurationFile !== null;
}




public function configurationFile(): string
{
if (!$this->hasConfigurationFile()) {
throw new Exception;
}

return $this->configurationFile;
}

/**
@phpstan-assert-if-true
*/
public function hasCoverageFilter(): bool
{
return $this->coverageFilter !== null;
}






public function coverageFilter(): array
{
if (!$this->hasCoverageFilter()) {
throw new Exception;
}

return $this->coverageFilter;
}

/**
@phpstan-assert-if-true
*/
public function hasCoverageClover(): bool
{
return $this->coverageClover !== null;
}




public function coverageClover(): string
{
if (!$this->hasCoverageClover()) {
throw new Exception;
}

return $this->coverageClover;
}

/**
@phpstan-assert-if-true
*/
public function hasCoverageCobertura(): bool
{
return $this->coverageCobertura !== null;
}




public function coverageCobertura(): string
{
if (!$this->hasCoverageCobertura()) {
throw new Exception;
}

return $this->coverageCobertura;
}

/**
@phpstan-assert-if-true
*/
public function hasCoverageCrap4J(): bool
{
return $this->coverageCrap4J !== null;
}




public function coverageCrap4J(): string
{
if (!$this->hasCoverageCrap4J()) {
throw new Exception;
}

return $this->coverageCrap4J;
}

/**
@phpstan-assert-if-true
*/
public function hasCoverageHtml(): bool
{
return $this->coverageHtml !== null;
}




public function coverageHtml(): string
{
if (!$this->hasCoverageHtml()) {
throw new Exception;
}

return $this->coverageHtml;
}

/**
@phpstan-assert-if-true
*/
public function hasCoveragePhp(): bool
{
return $this->coveragePhp !== null;
}




public function coveragePhp(): string
{
if (!$this->hasCoveragePhp()) {
throw new Exception;
}

return $this->coveragePhp;
}

/**
@phpstan-assert-if-true
*/
public function hasCoverageText(): bool
{
return $this->coverageText !== null;
}




public function coverageText(): string
{
if (!$this->hasCoverageText()) {
throw new Exception;
}

return $this->coverageText;
}

/**
@phpstan-assert-if-true
*/
public function hasCoverageTextShowUncoveredFiles(): bool
{
return $this->coverageTextShowUncoveredFiles !== null;
}




public function coverageTextShowUncoveredFiles(): bool
{
if (!$this->hasCoverageTextShowUncoveredFiles()) {
throw new Exception;
}

return $this->coverageTextShowUncoveredFiles;
}

/**
@phpstan-assert-if-true
*/
public function hasCoverageTextShowOnlySummary(): bool
{
return $this->coverageTextShowOnlySummary !== null;
}




public function coverageTextShowOnlySummary(): bool
{
if (!$this->hasCoverageTextShowOnlySummary()) {
throw new Exception;
}

return $this->coverageTextShowOnlySummary;
}

/**
@phpstan-assert-if-true
*/
public function hasCoverageXml(): bool
{
return $this->coverageXml !== null;
}




public function coverageXml(): string
{
if (!$this->hasCoverageXml()) {
throw new Exception;
}

return $this->coverageXml;
}

/**
@phpstan-assert-if-true
*/
public function hasPathCoverage(): bool
{
return $this->pathCoverage !== null;
}




public function pathCoverage(): bool
{
if (!$this->hasPathCoverage()) {
throw new Exception;
}

return $this->pathCoverage;
}

public function warmCoverageCache(): bool
{
return $this->warmCoverageCache;
}

/**
@phpstan-assert-if-true
*/
public function hasDefaultTimeLimit(): bool
{
return $this->defaultTimeLimit !== null;
}




public function defaultTimeLimit(): int
{
if (!$this->hasDefaultTimeLimit()) {
throw new Exception;
}

return $this->defaultTimeLimit;
}

/**
@phpstan-assert-if-true
*/
public function hasDisableCodeCoverageIgnore(): bool
{
return $this->disableCodeCoverageIgnore !== null;
}




public function disableCodeCoverageIgnore(): bool
{
if (!$this->hasDisableCodeCoverageIgnore()) {
throw new Exception;
}

return $this->disableCodeCoverageIgnore;
}

/**
@phpstan-assert-if-true
*/
public function hasDisallowTestOutput(): bool
{
return $this->disallowTestOutput !== null;
}




public function disallowTestOutput(): bool
{
if (!$this->hasDisallowTestOutput()) {
throw new Exception;
}

return $this->disallowTestOutput;
}

/**
@phpstan-assert-if-true
*/
public function hasEnforceTimeLimit(): bool
{
return $this->enforceTimeLimit !== null;
}




public function enforceTimeLimit(): bool
{
if (!$this->hasEnforceTimeLimit()) {
throw new Exception;
}

return $this->enforceTimeLimit;
}

/**
@phpstan-assert-if-true
*/
public function hasExcludeGroups(): bool
{
return $this->excludeGroups !== null;
}






public function excludeGroups(): array
{
if (!$this->hasExcludeGroups()) {
throw new Exception;
}

return $this->excludeGroups;
}

/**
@phpstan-assert-if-true
*/
public function hasExecutionOrder(): bool
{
return $this->executionOrder !== null;
}




public function executionOrder(): int
{
if (!$this->hasExecutionOrder()) {
throw new Exception;
}

return $this->executionOrder;
}

/**
@phpstan-assert-if-true
*/
public function hasExecutionOrderDefects(): bool
{
return $this->executionOrderDefects !== null;
}




public function executionOrderDefects(): int
{
if (!$this->hasExecutionOrderDefects()) {
throw new Exception;
}

return $this->executionOrderDefects;
}

/**
@phpstan-assert-if-true
*/
public function hasFailOnDeprecation(): bool
{
return $this->failOnDeprecation !== null;
}




public function failOnDeprecation(): bool
{
if (!$this->hasFailOnDeprecation()) {
throw new Exception;
}

return $this->failOnDeprecation;
}

/**
@phpstan-assert-if-true
*/
public function hasFailOnPhpunitDeprecation(): bool
{
return $this->failOnPhpunitDeprecation !== null;
}




public function failOnPhpunitDeprecation(): bool
{
if (!$this->hasFailOnPhpunitDeprecation()) {
throw new Exception;
}

return $this->failOnPhpunitDeprecation;
}

/**
@phpstan-assert-if-true
*/
public function hasFailOnEmptyTestSuite(): bool
{
return $this->failOnEmptyTestSuite !== null;
}




public function failOnEmptyTestSuite(): bool
{
if (!$this->hasFailOnEmptyTestSuite()) {
throw new Exception;
}

return $this->failOnEmptyTestSuite;
}

/**
@phpstan-assert-if-true
*/
public function hasFailOnIncomplete(): bool
{
return $this->failOnIncomplete !== null;
}




public function failOnIncomplete(): bool
{
if (!$this->hasFailOnIncomplete()) {
throw new Exception;
}

return $this->failOnIncomplete;
}

/**
@phpstan-assert-if-true
*/
public function hasFailOnNotice(): bool
{
return $this->failOnNotice !== null;
}




public function failOnNotice(): bool
{
if (!$this->hasFailOnNotice()) {
throw new Exception;
}

return $this->failOnNotice;
}

/**
@phpstan-assert-if-true
*/
public function hasFailOnRisky(): bool
{
return $this->failOnRisky !== null;
}




public function failOnRisky(): bool
{
if (!$this->hasFailOnRisky()) {
throw new Exception;
}

return $this->failOnRisky;
}

/**
@phpstan-assert-if-true
*/
public function hasFailOnSkipped(): bool
{
return $this->failOnSkipped !== null;
}




public function failOnSkipped(): bool
{
if (!$this->hasFailOnSkipped()) {
throw new Exception;
}

return $this->failOnSkipped;
}

/**
@phpstan-assert-if-true
*/
public function hasFailOnWarning(): bool
{
return $this->failOnWarning !== null;
}




public function failOnWarning(): bool
{
if (!$this->hasFailOnWarning()) {
throw new Exception;
}

return $this->failOnWarning;
}

/**
@phpstan-assert-if-true
*/
public function hasStopOnDefect(): bool
{
return $this->stopOnDefect !== null;
}




public function stopOnDefect(): bool
{
if (!$this->hasStopOnDefect()) {
throw new Exception;
}

return $this->stopOnDefect;
}

/**
@phpstan-assert-if-true
*/
public function hasStopOnDeprecation(): bool
{
return $this->stopOnDeprecation !== null;
}




public function stopOnDeprecation(): bool
{
if (!$this->hasStopOnDeprecation()) {
throw new Exception;
}

return $this->stopOnDeprecation;
}

/**
@phpstan-assert-if-true
*/
public function hasSpecificDeprecationToStopOn(): bool
{
return $this->specificDeprecationToStopOn !== null;
}




public function specificDeprecationToStopOn(): string
{
if (!$this->hasSpecificDeprecationToStopOn()) {
throw new Exception;
}

return $this->specificDeprecationToStopOn;
}

/**
@phpstan-assert-if-true
*/
public function hasStopOnError(): bool
{
return $this->stopOnError !== null;
}




public function stopOnError(): bool
{
if (!$this->hasStopOnError()) {
throw new Exception;
}

return $this->stopOnError;
}

/**
@phpstan-assert-if-true
*/
public function hasStopOnFailure(): bool
{
return $this->stopOnFailure !== null;
}




public function stopOnFailure(): bool
{
if (!$this->hasStopOnFailure()) {
throw new Exception;
}

return $this->stopOnFailure;
}

/**
@phpstan-assert-if-true
*/
public function hasStopOnIncomplete(): bool
{
return $this->stopOnIncomplete !== null;
}




public function stopOnIncomplete(): bool
{
if (!$this->hasStopOnIncomplete()) {
throw new Exception;
}

return $this->stopOnIncomplete;
}

/**
@phpstan-assert-if-true
*/
public function hasStopOnNotice(): bool
{
return $this->stopOnNotice !== null;
}




public function stopOnNotice(): bool
{
if (!$this->hasStopOnNotice()) {
throw new Exception;
}

return $this->stopOnNotice;
}

/**
@phpstan-assert-if-true
*/
public function hasStopOnRisky(): bool
{
return $this->stopOnRisky !== null;
}




public function stopOnRisky(): bool
{
if (!$this->hasStopOnRisky()) {
throw new Exception;
}

return $this->stopOnRisky;
}

/**
@phpstan-assert-if-true
*/
public function hasStopOnSkipped(): bool
{
return $this->stopOnSkipped !== null;
}




public function stopOnSkipped(): bool
{
if (!$this->hasStopOnSkipped()) {
throw new Exception;
}

return $this->stopOnSkipped;
}

/**
@phpstan-assert-if-true
*/
public function hasStopOnWarning(): bool
{
return $this->stopOnWarning !== null;
}




public function stopOnWarning(): bool
{
if (!$this->hasStopOnWarning()) {
throw new Exception;
}

return $this->stopOnWarning;
}

/**
@phpstan-assert-if-true
*/
public function hasExcludeFilter(): bool
{
return $this->excludeFilter !== null;
}




public function excludeFilter(): string
{
if (!$this->hasExcludeFilter()) {
throw new Exception;
}

return $this->excludeFilter;
}

/**
@phpstan-assert-if-true
*/
public function hasFilter(): bool
{
return $this->filter !== null;
}




public function filter(): string
{
if (!$this->hasFilter()) {
throw new Exception;
}

return $this->filter;
}

/**
@phpstan-assert-if-true
*/
public function hasGenerateBaseline(): bool
{
return $this->generateBaseline !== null;
}




public function generateBaseline(): string
{
if (!$this->hasGenerateBaseline()) {
throw new Exception;
}

return $this->generateBaseline;
}

/**
@phpstan-assert-if-true
*/
public function hasUseBaseline(): bool
{
return $this->useBaseline !== null;
}




public function useBaseline(): string
{
if (!$this->hasUseBaseline()) {
throw new Exception;
}

return $this->useBaseline;
}

public function ignoreBaseline(): bool
{
return $this->ignoreBaseline;
}

public function generateConfiguration(): bool
{
return $this->generateConfiguration;
}

public function migrateConfiguration(): bool
{
return $this->migrateConfiguration;
}

/**
@phpstan-assert-if-true
*/
public function hasGroups(): bool
{
return $this->groups !== null;
}






public function groups(): array
{
if (!$this->hasGroups()) {
throw new Exception;
}

return $this->groups;
}

/**
@phpstan-assert-if-true
*/
public function hasTestsCovering(): bool
{
return $this->testsCovering !== null;
}






public function testsCovering(): array
{
if (!$this->hasTestsCovering()) {
throw new Exception;
}

return $this->testsCovering;
}

/**
@phpstan-assert-if-true
*/
public function hasTestsUsing(): bool
{
return $this->testsUsing !== null;
}






public function testsUsing(): array
{
if (!$this->hasTestsUsing()) {
throw new Exception;
}

return $this->testsUsing;
}

/**
@phpstan-assert-if-true
*/
public function hasTestsRequiringPhpExtension(): bool
{
return $this->testsRequiringPhpExtension !== null;
}






public function testsRequiringPhpExtension(): array
{
if (!$this->hasTestsRequiringPhpExtension()) {
throw new Exception;
}

return $this->testsRequiringPhpExtension;
}

public function help(): bool
{
return $this->help;
}

/**
@phpstan-assert-if-true
*/
public function hasIncludePath(): bool
{
return $this->includePath !== null;
}




public function includePath(): string
{
if (!$this->hasIncludePath()) {
throw new Exception;
}

return $this->includePath;
}

/**
@phpstan-assert-if-true
*/
public function hasIniSettings(): bool
{
return $this->iniSettings !== null;
}






public function iniSettings(): array
{
if (!$this->hasIniSettings()) {
throw new Exception;
}

return $this->iniSettings;
}

/**
@phpstan-assert-if-true
*/
public function hasJunitLogfile(): bool
{
return $this->junitLogfile !== null;
}




public function junitLogfile(): string
{
if (!$this->hasJunitLogfile()) {
throw new Exception;
}

return $this->junitLogfile;
}

public function listGroups(): bool
{
return $this->listGroups;
}

public function listSuites(): bool
{
return $this->listSuites;
}

public function listTestFiles(): bool
{
return $this->listTestFiles;
}

public function listTests(): bool
{
return $this->listTests;
}

/**
@phpstan-assert-if-true
*/
public function hasListTestsXml(): bool
{
return $this->listTestsXml !== null;
}




public function listTestsXml(): string
{
if (!$this->hasListTestsXml()) {
throw new Exception;
}

return $this->listTestsXml;
}

/**
@phpstan-assert-if-true
*/
public function hasNoCoverage(): bool
{
return $this->noCoverage !== null;
}




public function noCoverage(): bool
{
if (!$this->hasNoCoverage()) {
throw new Exception;
}

return $this->noCoverage;
}

/**
@phpstan-assert-if-true
*/
public function hasNoExtensions(): bool
{
return $this->noExtensions !== null;
}




public function noExtensions(): bool
{
if (!$this->hasNoExtensions()) {
throw new Exception;
}

return $this->noExtensions;
}

/**
@phpstan-assert-if-true
*/
public function hasNoOutput(): bool
{
return $this->noOutput !== null;
}




public function noOutput(): bool
{
if ($this->noOutput === null) {
throw new Exception;
}

return $this->noOutput;
}

/**
@phpstan-assert-if-true
*/
public function hasNoProgress(): bool
{
return $this->noProgress !== null;
}




public function noProgress(): bool
{
if ($this->noProgress === null) {
throw new Exception;
}

return $this->noProgress;
}

/**
@phpstan-assert-if-true
*/
public function hasNoResults(): bool
{
return $this->noResults !== null;
}




public function noResults(): bool
{
if ($this->noResults === null) {
throw new Exception;
}

return $this->noResults;
}

/**
@phpstan-assert-if-true
*/
public function hasNoLogging(): bool
{
return $this->noLogging !== null;
}




public function noLogging(): bool
{
if (!$this->hasNoLogging()) {
throw new Exception;
}

return $this->noLogging;
}

/**
@phpstan-assert-if-true
*/
public function hasProcessIsolation(): bool
{
return $this->processIsolation !== null;
}




public function processIsolation(): bool
{
if (!$this->hasProcessIsolation()) {
throw new Exception;
}

return $this->processIsolation;
}

/**
@phpstan-assert-if-true
*/
public function hasRandomOrderSeed(): bool
{
return $this->randomOrderSeed !== null;
}




public function randomOrderSeed(): int
{
if (!$this->hasRandomOrderSeed()) {
throw new Exception;
}

return $this->randomOrderSeed;
}

/**
@phpstan-assert-if-true
*/
public function hasReportUselessTests(): bool
{
return $this->reportUselessTests !== null;
}




public function reportUselessTests(): bool
{
if (!$this->hasReportUselessTests()) {
throw new Exception;
}

return $this->reportUselessTests;
}

/**
@phpstan-assert-if-true
*/
public function hasResolveDependencies(): bool
{
return $this->resolveDependencies !== null;
}




public function resolveDependencies(): bool
{
if (!$this->hasResolveDependencies()) {
throw new Exception;
}

return $this->resolveDependencies;
}

/**
@phpstan-assert-if-true
*/
public function hasReverseList(): bool
{
return $this->reverseList !== null;
}




public function reverseList(): bool
{
if (!$this->hasReverseList()) {
throw new Exception;
}

return $this->reverseList;
}

/**
@phpstan-assert-if-true
*/
public function hasStderr(): bool
{
return $this->stderr !== null;
}




public function stderr(): bool
{
if (!$this->hasStderr()) {
throw new Exception;
}

return $this->stderr;
}

/**
@phpstan-assert-if-true
*/
public function hasStrictCoverage(): bool
{
return $this->strictCoverage !== null;
}




public function strictCoverage(): bool
{
if (!$this->hasStrictCoverage()) {
throw new Exception;
}

return $this->strictCoverage;
}

/**
@phpstan-assert-if-true
*/
public function hasTeamcityLogfile(): bool
{
return $this->teamcityLogfile !== null;
}




public function teamcityLogfile(): string
{
if (!$this->hasTeamcityLogfile()) {
throw new Exception;
}

return $this->teamcityLogfile;
}

/**
@phpstan-assert-if-true
*/
public function hasTeamCityPrinter(): bool
{
return $this->teamCityPrinter !== null;
}




public function teamCityPrinter(): bool
{
if (!$this->hasTeamCityPrinter()) {
throw new Exception;
}

return $this->teamCityPrinter;
}

/**
@phpstan-assert-if-true
*/
public function hasTestdoxHtmlFile(): bool
{
return $this->testdoxHtmlFile !== null;
}




public function testdoxHtmlFile(): string
{
if (!$this->hasTestdoxHtmlFile()) {
throw new Exception;
}

return $this->testdoxHtmlFile;
}

/**
@phpstan-assert-if-true
*/
public function hasTestdoxTextFile(): bool
{
return $this->testdoxTextFile !== null;
}




public function testdoxTextFile(): string
{
if (!$this->hasTestdoxTextFile()) {
throw new Exception;
}

return $this->testdoxTextFile;
}

/**
@phpstan-assert-if-true
*/
public function hasTestDoxPrinter(): bool
{
return $this->testdoxPrinter !== null;
}




public function testdoxPrinter(): bool
{
if (!$this->hasTestDoxPrinter()) {
throw new Exception;
}

return $this->testdoxPrinter;
}

/**
@phpstan-assert-if-true
*/
public function hasTestDoxPrinterSummary(): bool
{
return $this->testdoxPrinterSummary !== null;
}




public function testdoxPrinterSummary(): bool
{
if (!$this->hasTestDoxPrinterSummary()) {
throw new Exception;
}

return $this->testdoxPrinterSummary;
}

/**
@phpstan-assert-if-true
*/
public function hasTestSuffixes(): bool
{
return $this->testSuffixes !== null;
}






public function testSuffixes(): array
{
if (!$this->hasTestSuffixes()) {
throw new Exception;
}

return $this->testSuffixes;
}

/**
@phpstan-assert-if-true
*/
public function hasTestSuite(): bool
{
return $this->testSuite !== null;
}




public function testSuite(): string
{
if (!$this->hasTestSuite()) {
throw new Exception;
}

return $this->testSuite;
}

/**
@phpstan-assert-if-true
*/
public function hasExcludedTestSuite(): bool
{
return $this->excludeTestSuite !== null;
}




public function excludedTestSuite(): string
{
if (!$this->hasExcludedTestSuite()) {
throw new Exception;
}

return $this->excludeTestSuite;
}

public function useDefaultConfiguration(): bool
{
return $this->useDefaultConfiguration;
}

/**
@phpstan-assert-if-true
*/
public function hasDisplayDetailsOnIncompleteTests(): bool
{
return $this->displayDetailsOnIncompleteTests !== null;
}




public function displayDetailsOnIncompleteTests(): bool
{
if (!$this->hasDisplayDetailsOnIncompleteTests()) {
throw new Exception;
}

return $this->displayDetailsOnIncompleteTests;
}

/**
@phpstan-assert-if-true
*/
public function hasDisplayDetailsOnSkippedTests(): bool
{
return $this->displayDetailsOnSkippedTests !== null;
}




public function displayDetailsOnSkippedTests(): bool
{
if (!$this->hasDisplayDetailsOnSkippedTests()) {
throw new Exception;
}

return $this->displayDetailsOnSkippedTests;
}

/**
@phpstan-assert-if-true
*/
public function hasDisplayDetailsOnTestsThatTriggerDeprecations(): bool
{
return $this->displayDetailsOnTestsThatTriggerDeprecations !== null;
}




public function displayDetailsOnTestsThatTriggerDeprecations(): bool
{
if (!$this->hasDisplayDetailsOnTestsThatTriggerDeprecations()) {
throw new Exception;
}

return $this->displayDetailsOnTestsThatTriggerDeprecations;
}

/**
@phpstan-assert-if-true
*/
public function hasDisplayDetailsOnPhpunitDeprecations(): bool
{
return $this->displayDetailsOnPhpunitDeprecations !== null;
}




public function displayDetailsOnPhpunitDeprecations(): bool
{
if (!$this->hasDisplayDetailsOnPhpunitDeprecations()) {
throw new Exception;
}

return $this->displayDetailsOnPhpunitDeprecations;
}

/**
@phpstan-assert-if-true
*/
public function hasDisplayDetailsOnTestsThatTriggerErrors(): bool
{
return $this->displayDetailsOnTestsThatTriggerErrors !== null;
}




public function displayDetailsOnTestsThatTriggerErrors(): bool
{
if (!$this->hasDisplayDetailsOnTestsThatTriggerErrors()) {
throw new Exception;
}

return $this->displayDetailsOnTestsThatTriggerErrors;
}

/**
@phpstan-assert-if-true
*/
public function hasDisplayDetailsOnTestsThatTriggerNotices(): bool
{
return $this->displayDetailsOnTestsThatTriggerNotices !== null;
}




public function displayDetailsOnTestsThatTriggerNotices(): bool
{
if (!$this->hasDisplayDetailsOnTestsThatTriggerNotices()) {
throw new Exception;
}

return $this->displayDetailsOnTestsThatTriggerNotices;
}

/**
@phpstan-assert-if-true
*/
public function hasDisplayDetailsOnTestsThatTriggerWarnings(): bool
{
return $this->displayDetailsOnTestsThatTriggerWarnings !== null;
}




public function displayDetailsOnTestsThatTriggerWarnings(): bool
{
if (!$this->hasDisplayDetailsOnTestsThatTriggerWarnings()) {
throw new Exception;
}

return $this->displayDetailsOnTestsThatTriggerWarnings;
}

public function version(): bool
{
return $this->version;
}

/**
@phpstan-assert-if-true
*/
public function hasLogEventsText(): bool
{
return $this->logEventsText !== null;
}




public function logEventsText(): string
{
if (!$this->hasLogEventsText()) {
throw new Exception;
}

return $this->logEventsText;
}

/**
@phpstan-assert-if-true
*/
public function hasLogEventsVerboseText(): bool
{
return $this->logEventsVerboseText !== null;
}




public function logEventsVerboseText(): string
{
if (!$this->hasLogEventsVerboseText()) {
throw new Exception;
}

return $this->logEventsVerboseText;
}

public function debug(): bool
{
return $this->debug;
}

/**
@phpstan-assert-if-true
*/
public function hasExtensions(): bool
{
return $this->extensions !== null;
}






public function extensions(): array
{
if (!$this->hasExtensions()) {
throw new Exception;
}

return $this->extensions;
}
}
