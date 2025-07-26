<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Configuration;

/**
@immutable
@no-named-arguments

*/
final readonly class Configuration
{
public const COLOR_NEVER = 'never';
public const COLOR_AUTO = 'auto';
public const COLOR_ALWAYS = 'always';
public const COLOR_DEFAULT = self::COLOR_NEVER;




private array $cliArguments;
private ?string $configurationFile;
private ?string $bootstrap;
private bool $cacheResult;
private ?string $cacheDirectory;
private ?string $coverageCacheDirectory;
private Source $source;
private bool $pathCoverage;
private ?string $coverageClover;
private ?string $coverageCobertura;
private ?string $coverageCrap4j;
private int $coverageCrap4jThreshold;
private ?string $coverageHtml;
private int $coverageHtmlLowUpperBound;
private int $coverageHtmlHighLowerBound;
private string $coverageHtmlColorSuccessLow;
private string $coverageHtmlColorSuccessMedium;
private string $coverageHtmlColorSuccessHigh;
private string $coverageHtmlColorWarning;
private string $coverageHtmlColorDanger;
private ?string $coverageHtmlCustomCssFile;
private ?string $coveragePhp;
private ?string $coverageText;
private bool $coverageTextShowUncoveredFiles;
private bool $coverageTextShowOnlySummary;
private ?string $coverageXml;
private string $testResultCacheFile;
private bool $ignoreDeprecatedCodeUnitsFromCodeCoverage;
private bool $disableCodeCoverageIgnore;
private bool $failOnDeprecation;
private bool $failOnPhpunitDeprecation;
private bool $failOnEmptyTestSuite;
private bool $failOnIncomplete;
private bool $failOnNotice;
private bool $failOnRisky;
private bool $failOnSkipped;
private bool $failOnWarning;
private bool $stopOnDefect;
private bool $stopOnDeprecation;
private ?string $specificDeprecationToStopOn;
private bool $stopOnError;
private bool $stopOnFailure;
private bool $stopOnIncomplete;
private bool $stopOnNotice;
private bool $stopOnRisky;
private bool $stopOnSkipped;
private bool $stopOnWarning;
private bool $outputToStandardErrorStream;
private int $columns;
private bool $noExtensions;




private ?string $pharExtensionDirectory;




private array $extensionBootstrappers;
private bool $backupGlobals;
private bool $backupStaticProperties;
private bool $beStrictAboutChangesToGlobalState;
private bool $colors;
private bool $processIsolation;
private bool $enforceTimeLimit;
private int $defaultTimeLimit;
private int $timeoutForSmallTests;
private int $timeoutForMediumTests;
private int $timeoutForLargeTests;
private bool $reportUselessTests;
private bool $strictCoverage;
private bool $disallowTestOutput;
private bool $displayDetailsOnIncompleteTests;
private bool $displayDetailsOnSkippedTests;
private bool $displayDetailsOnTestsThatTriggerDeprecations;
private bool $displayDetailsOnPhpunitDeprecations;
private bool $displayDetailsOnTestsThatTriggerErrors;
private bool $displayDetailsOnTestsThatTriggerNotices;
private bool $displayDetailsOnTestsThatTriggerWarnings;
private bool $reverseDefectList;
private bool $requireCoverageMetadata;
private bool $noProgress;
private bool $noResults;
private bool $noOutput;
private int $executionOrder;
private int $executionOrderDefects;
private bool $resolveDependencies;
private ?string $logfileTeamcity;
private ?string $logfileJunit;
private ?string $logfileTestdoxHtml;
private ?string $logfileTestdoxText;
private ?string $logEventsText;
private ?string $logEventsVerboseText;




private ?array $testsCovering;




private ?array $testsUsing;




private ?array $testsRequiringPhpExtension;
private bool $teamCityOutput;
private bool $testDoxOutput;
private bool $testDoxOutputSummary;
private ?string $filter;
private ?string $excludeFilter;




private array $groups;




private array $excludeGroups;
private int $randomOrderSeed;
private bool $includeUncoveredFiles;
private TestSuiteCollection $testSuite;
private string $includeTestSuite;
private string $excludeTestSuite;
private ?string $defaultTestSuite;




private array $testSuffixes;
private Php $php;
private bool $controlGarbageCollector;
private int $numberOfTestsBeforeGarbageCollection;
private ?string $generateBaseline;
private bool $debug;




private int $shortenArraysForExportThreshold;













public function __construct(array $cliArguments, ?string $configurationFile, ?string $bootstrap, bool $cacheResult, ?string $cacheDirectory, ?string $coverageCacheDirectory, Source $source, string $testResultCacheFile, ?string $coverageClover, ?string $coverageCobertura, ?string $coverageCrap4j, int $coverageCrap4jThreshold, ?string $coverageHtml, int $coverageHtmlLowUpperBound, int $coverageHtmlHighLowerBound, string $coverageHtmlColorSuccessLow, string $coverageHtmlColorSuccessMedium, string $coverageHtmlColorSuccessHigh, string $coverageHtmlColorWarning, string $coverageHtmlColorDanger, ?string $coverageHtmlCustomCssFile, ?string $coveragePhp, ?string $coverageText, bool $coverageTextShowUncoveredFiles, bool $coverageTextShowOnlySummary, ?string $coverageXml, bool $pathCoverage, bool $ignoreDeprecatedCodeUnitsFromCodeCoverage, bool $disableCodeCoverageIgnore, bool $failOnDeprecation, bool $failOnPhpunitDeprecation, bool $failOnEmptyTestSuite, bool $failOnIncomplete, bool $failOnNotice, bool $failOnRisky, bool $failOnSkipped, bool $failOnWarning, bool $stopOnDefect, bool $stopOnDeprecation, ?string $specificDeprecationToStopOn, bool $stopOnError, bool $stopOnFailure, bool $stopOnIncomplete, bool $stopOnNotice, bool $stopOnRisky, bool $stopOnSkipped, bool $stopOnWarning, bool $outputToStandardErrorStream, int|string $columns, bool $noExtensions, ?string $pharExtensionDirectory, array $extensionBootstrappers, bool $backupGlobals, bool $backupStaticProperties, bool $beStrictAboutChangesToGlobalState, bool $colors, bool $processIsolation, bool $enforceTimeLimit, int $defaultTimeLimit, int $timeoutForSmallTests, int $timeoutForMediumTests, int $timeoutForLargeTests, bool $reportUselessTests, bool $strictCoverage, bool $disallowTestOutput, bool $displayDetailsOnIncompleteTests, bool $displayDetailsOnSkippedTests, bool $displayDetailsOnTestsThatTriggerDeprecations, bool $displayDetailsOnPhpunitDeprecations, bool $displayDetailsOnTestsThatTriggerErrors, bool $displayDetailsOnTestsThatTriggerNotices, bool $displayDetailsOnTestsThatTriggerWarnings, bool $reverseDefectList, bool $requireCoverageMetadata, bool $noProgress, bool $noResults, bool $noOutput, int $executionOrder, int $executionOrderDefects, bool $resolveDependencies, ?string $logfileTeamcity, ?string $logfileJunit, ?string $logfileTestdoxHtml, ?string $logfileTestdoxText, ?string $logEventsText, ?string $logEventsVerboseText, bool $teamCityOutput, bool $testDoxOutput, bool $testDoxOutputSummary, ?array $testsCovering, ?array $testsUsing, ?array $testsRequiringPhpExtension, ?string $filter, ?string $excludeFilter, array $groups, array $excludeGroups, int $randomOrderSeed, bool $includeUncoveredFiles, TestSuiteCollection $testSuite, string $includeTestSuite, string $excludeTestSuite, ?string $defaultTestSuite, array $testSuffixes, Php $php, bool $controlGarbageCollector, int $numberOfTestsBeforeGarbageCollection, ?string $generateBaseline, bool $debug, int $shortenArraysForExportThreshold)
{
$this->cliArguments = $cliArguments;
$this->configurationFile = $configurationFile;
$this->bootstrap = $bootstrap;
$this->cacheResult = $cacheResult;
$this->cacheDirectory = $cacheDirectory;
$this->coverageCacheDirectory = $coverageCacheDirectory;
$this->source = $source;
$this->testResultCacheFile = $testResultCacheFile;
$this->coverageClover = $coverageClover;
$this->coverageCobertura = $coverageCobertura;
$this->coverageCrap4j = $coverageCrap4j;
$this->coverageCrap4jThreshold = $coverageCrap4jThreshold;
$this->coverageHtml = $coverageHtml;
$this->coverageHtmlLowUpperBound = $coverageHtmlLowUpperBound;
$this->coverageHtmlHighLowerBound = $coverageHtmlHighLowerBound;
$this->coverageHtmlColorSuccessLow = $coverageHtmlColorSuccessLow;
$this->coverageHtmlColorSuccessMedium = $coverageHtmlColorSuccessMedium;
$this->coverageHtmlColorSuccessHigh = $coverageHtmlColorSuccessHigh;
$this->coverageHtmlColorWarning = $coverageHtmlColorWarning;
$this->coverageHtmlColorDanger = $coverageHtmlColorDanger;
$this->coverageHtmlCustomCssFile = $coverageHtmlCustomCssFile;
$this->coveragePhp = $coveragePhp;
$this->coverageText = $coverageText;
$this->coverageTextShowUncoveredFiles = $coverageTextShowUncoveredFiles;
$this->coverageTextShowOnlySummary = $coverageTextShowOnlySummary;
$this->coverageXml = $coverageXml;
$this->pathCoverage = $pathCoverage;
$this->ignoreDeprecatedCodeUnitsFromCodeCoverage = $ignoreDeprecatedCodeUnitsFromCodeCoverage;
$this->disableCodeCoverageIgnore = $disableCodeCoverageIgnore;
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
$this->outputToStandardErrorStream = $outputToStandardErrorStream;
$this->columns = $columns;
$this->noExtensions = $noExtensions;
$this->pharExtensionDirectory = $pharExtensionDirectory;
$this->extensionBootstrappers = $extensionBootstrappers;
$this->backupGlobals = $backupGlobals;
$this->backupStaticProperties = $backupStaticProperties;
$this->beStrictAboutChangesToGlobalState = $beStrictAboutChangesToGlobalState;
$this->colors = $colors;
$this->processIsolation = $processIsolation;
$this->enforceTimeLimit = $enforceTimeLimit;
$this->defaultTimeLimit = $defaultTimeLimit;
$this->timeoutForSmallTests = $timeoutForSmallTests;
$this->timeoutForMediumTests = $timeoutForMediumTests;
$this->timeoutForLargeTests = $timeoutForLargeTests;
$this->reportUselessTests = $reportUselessTests;
$this->strictCoverage = $strictCoverage;
$this->disallowTestOutput = $disallowTestOutput;
$this->displayDetailsOnIncompleteTests = $displayDetailsOnIncompleteTests;
$this->displayDetailsOnSkippedTests = $displayDetailsOnSkippedTests;
$this->displayDetailsOnTestsThatTriggerDeprecations = $displayDetailsOnTestsThatTriggerDeprecations;
$this->displayDetailsOnPhpunitDeprecations = $displayDetailsOnPhpunitDeprecations;
$this->displayDetailsOnTestsThatTriggerErrors = $displayDetailsOnTestsThatTriggerErrors;
$this->displayDetailsOnTestsThatTriggerNotices = $displayDetailsOnTestsThatTriggerNotices;
$this->displayDetailsOnTestsThatTriggerWarnings = $displayDetailsOnTestsThatTriggerWarnings;
$this->reverseDefectList = $reverseDefectList;
$this->requireCoverageMetadata = $requireCoverageMetadata;
$this->noProgress = $noProgress;
$this->noResults = $noResults;
$this->noOutput = $noOutput;
$this->executionOrder = $executionOrder;
$this->executionOrderDefects = $executionOrderDefects;
$this->resolveDependencies = $resolveDependencies;
$this->logfileTeamcity = $logfileTeamcity;
$this->logfileJunit = $logfileJunit;
$this->logfileTestdoxHtml = $logfileTestdoxHtml;
$this->logfileTestdoxText = $logfileTestdoxText;
$this->logEventsText = $logEventsText;
$this->logEventsVerboseText = $logEventsVerboseText;
$this->teamCityOutput = $teamCityOutput;
$this->testDoxOutput = $testDoxOutput;
$this->testDoxOutputSummary = $testDoxOutputSummary;
$this->testsCovering = $testsCovering;
$this->testsUsing = $testsUsing;
$this->testsRequiringPhpExtension = $testsRequiringPhpExtension;
$this->filter = $filter;
$this->excludeFilter = $excludeFilter;
$this->groups = $groups;
$this->excludeGroups = $excludeGroups;
$this->randomOrderSeed = $randomOrderSeed;
$this->includeUncoveredFiles = $includeUncoveredFiles;
$this->testSuite = $testSuite;
$this->includeTestSuite = $includeTestSuite;
$this->excludeTestSuite = $excludeTestSuite;
$this->defaultTestSuite = $defaultTestSuite;
$this->testSuffixes = $testSuffixes;
$this->php = $php;
$this->controlGarbageCollector = $controlGarbageCollector;
$this->numberOfTestsBeforeGarbageCollection = $numberOfTestsBeforeGarbageCollection;
$this->generateBaseline = $generateBaseline;
$this->debug = $debug;
$this->shortenArraysForExportThreshold = $shortenArraysForExportThreshold;
}

/**
@phpstan-assert-if-true
*/
public function hasCliArguments(): bool
{
return !empty($this->cliArguments);
}




public function cliArguments(): array
{
return $this->cliArguments;
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
throw new NoConfigurationFileException;
}

return $this->configurationFile;
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
throw new NoBootstrapException;
}

return $this->bootstrap;
}

public function cacheResult(): bool
{
return $this->cacheResult;
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
throw new NoCacheDirectoryException;
}

return $this->cacheDirectory;
}

/**
@phpstan-assert-if-true
*/
public function hasCoverageCacheDirectory(): bool
{
return $this->coverageCacheDirectory !== null;
}




public function coverageCacheDirectory(): string
{
if (!$this->hasCoverageCacheDirectory()) {
throw new NoCoverageCacheDirectoryException;
}

return $this->coverageCacheDirectory;
}

public function source(): Source
{
return $this->source;
}

public function testResultCacheFile(): string
{
return $this->testResultCacheFile;
}

public function ignoreDeprecatedCodeUnitsFromCodeCoverage(): bool
{
return $this->ignoreDeprecatedCodeUnitsFromCodeCoverage;
}

public function disableCodeCoverageIgnore(): bool
{
return $this->disableCodeCoverageIgnore;
}

public function pathCoverage(): bool
{
return $this->pathCoverage;
}

public function hasCoverageReport(): bool
{
return $this->hasCoverageClover() ||
$this->hasCoverageCobertura() ||
$this->hasCoverageCrap4j() ||
$this->hasCoverageHtml() ||
$this->hasCoveragePhp() ||
$this->hasCoverageText() ||
$this->hasCoverageXml();
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
throw new CodeCoverageReportNotConfiguredException;
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
throw new CodeCoverageReportNotConfiguredException;
}

return $this->coverageCobertura;
}

/**
@phpstan-assert-if-true
*/
public function hasCoverageCrap4j(): bool
{
return $this->coverageCrap4j !== null;
}




public function coverageCrap4j(): string
{
if (!$this->hasCoverageCrap4j()) {
throw new CodeCoverageReportNotConfiguredException;
}

return $this->coverageCrap4j;
}

public function coverageCrap4jThreshold(): int
{
return $this->coverageCrap4jThreshold;
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
throw new CodeCoverageReportNotConfiguredException;
}

return $this->coverageHtml;
}

public function coverageHtmlLowUpperBound(): int
{
return $this->coverageHtmlLowUpperBound;
}

public function coverageHtmlHighLowerBound(): int
{
return $this->coverageHtmlHighLowerBound;
}

public function coverageHtmlColorSuccessLow(): string
{
return $this->coverageHtmlColorSuccessLow;
}

public function coverageHtmlColorSuccessMedium(): string
{
return $this->coverageHtmlColorSuccessMedium;
}

public function coverageHtmlColorSuccessHigh(): string
{
return $this->coverageHtmlColorSuccessHigh;
}

public function coverageHtmlColorWarning(): string
{
return $this->coverageHtmlColorWarning;
}

public function coverageHtmlColorDanger(): string
{
return $this->coverageHtmlColorDanger;
}

/**
@phpstan-assert-if-true
*/
public function hasCoverageHtmlCustomCssFile(): bool
{
return $this->coverageHtmlCustomCssFile !== null;
}




public function coverageHtmlCustomCssFile(): string
{
if (!$this->hasCoverageHtmlCustomCssFile()) {
throw new NoCustomCssFileException;
}

return $this->coverageHtmlCustomCssFile;
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
throw new CodeCoverageReportNotConfiguredException;
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
throw new CodeCoverageReportNotConfiguredException;
}

return $this->coverageText;
}

public function coverageTextShowUncoveredFiles(): bool
{
return $this->coverageTextShowUncoveredFiles;
}

public function coverageTextShowOnlySummary(): bool
{
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
throw new CodeCoverageReportNotConfiguredException;
}

return $this->coverageXml;
}

public function failOnDeprecation(): bool
{
return $this->failOnDeprecation;
}

public function failOnPhpunitDeprecation(): bool
{
return $this->failOnPhpunitDeprecation;
}

public function failOnEmptyTestSuite(): bool
{
return $this->failOnEmptyTestSuite;
}

public function failOnIncomplete(): bool
{
return $this->failOnIncomplete;
}

public function failOnNotice(): bool
{
return $this->failOnNotice;
}

public function failOnRisky(): bool
{
return $this->failOnRisky;
}

public function failOnSkipped(): bool
{
return $this->failOnSkipped;
}

public function failOnWarning(): bool
{
return $this->failOnWarning;
}

public function stopOnDefect(): bool
{
return $this->stopOnDefect;
}

public function stopOnDeprecation(): bool
{
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
throw new SpecificDeprecationToStopOnNotConfiguredException;
}

return $this->specificDeprecationToStopOn;
}

public function stopOnError(): bool
{
return $this->stopOnError;
}

public function stopOnFailure(): bool
{
return $this->stopOnFailure;
}

public function stopOnIncomplete(): bool
{
return $this->stopOnIncomplete;
}

public function stopOnNotice(): bool
{
return $this->stopOnNotice;
}

public function stopOnRisky(): bool
{
return $this->stopOnRisky;
}

public function stopOnSkipped(): bool
{
return $this->stopOnSkipped;
}

public function stopOnWarning(): bool
{
return $this->stopOnWarning;
}

public function outputToStandardErrorStream(): bool
{
return $this->outputToStandardErrorStream;
}

public function columns(): int
{
return $this->columns;
}

public function noExtensions(): bool
{
return $this->noExtensions;
}

/**
@phpstan-assert-if-true
*/
public function hasPharExtensionDirectory(): bool
{
return $this->pharExtensionDirectory !== null;
}






public function pharExtensionDirectory(): string
{
if (!$this->hasPharExtensionDirectory()) {
throw new NoPharExtensionDirectoryException;
}

return $this->pharExtensionDirectory;
}




public function extensionBootstrappers(): array
{
return $this->extensionBootstrappers;
}

public function backupGlobals(): bool
{
return $this->backupGlobals;
}

public function backupStaticProperties(): bool
{
return $this->backupStaticProperties;
}

public function beStrictAboutChangesToGlobalState(): bool
{
return $this->beStrictAboutChangesToGlobalState;
}

public function colors(): bool
{
return $this->colors;
}

public function processIsolation(): bool
{
return $this->processIsolation;
}

public function enforceTimeLimit(): bool
{
return $this->enforceTimeLimit;
}

public function defaultTimeLimit(): int
{
return $this->defaultTimeLimit;
}

public function timeoutForSmallTests(): int
{
return $this->timeoutForSmallTests;
}

public function timeoutForMediumTests(): int
{
return $this->timeoutForMediumTests;
}

public function timeoutForLargeTests(): int
{
return $this->timeoutForLargeTests;
}

public function reportUselessTests(): bool
{
return $this->reportUselessTests;
}

public function strictCoverage(): bool
{
return $this->strictCoverage;
}

public function disallowTestOutput(): bool
{
return $this->disallowTestOutput;
}

public function displayDetailsOnIncompleteTests(): bool
{
return $this->displayDetailsOnIncompleteTests;
}

public function displayDetailsOnSkippedTests(): bool
{
return $this->displayDetailsOnSkippedTests;
}

public function displayDetailsOnTestsThatTriggerDeprecations(): bool
{
return $this->displayDetailsOnTestsThatTriggerDeprecations;
}

public function displayDetailsOnPhpunitDeprecations(): bool
{
return $this->displayDetailsOnPhpunitDeprecations;
}

public function displayDetailsOnTestsThatTriggerErrors(): bool
{
return $this->displayDetailsOnTestsThatTriggerErrors;
}

public function displayDetailsOnTestsThatTriggerNotices(): bool
{
return $this->displayDetailsOnTestsThatTriggerNotices;
}

public function displayDetailsOnTestsThatTriggerWarnings(): bool
{
return $this->displayDetailsOnTestsThatTriggerWarnings;
}

public function reverseDefectList(): bool
{
return $this->reverseDefectList;
}

public function requireCoverageMetadata(): bool
{
return $this->requireCoverageMetadata;
}

public function noProgress(): bool
{
return $this->noProgress;
}

public function noResults(): bool
{
return $this->noResults;
}

public function noOutput(): bool
{
return $this->noOutput;
}

public function executionOrder(): int
{
return $this->executionOrder;
}

public function executionOrderDefects(): int
{
return $this->executionOrderDefects;
}

public function resolveDependencies(): bool
{
return $this->resolveDependencies;
}

/**
@phpstan-assert-if-true
*/
public function hasLogfileTeamcity(): bool
{
return $this->logfileTeamcity !== null;
}




public function logfileTeamcity(): string
{
if (!$this->hasLogfileTeamcity()) {
throw new LoggingNotConfiguredException;
}

return $this->logfileTeamcity;
}

/**
@phpstan-assert-if-true
*/
public function hasLogfileJunit(): bool
{
return $this->logfileJunit !== null;
}




public function logfileJunit(): string
{
if (!$this->hasLogfileJunit()) {
throw new LoggingNotConfiguredException;
}

return $this->logfileJunit;
}

/**
@phpstan-assert-if-true
*/
public function hasLogfileTestdoxHtml(): bool
{
return $this->logfileTestdoxHtml !== null;
}




public function logfileTestdoxHtml(): string
{
if (!$this->hasLogfileTestdoxHtml()) {
throw new LoggingNotConfiguredException;
}

return $this->logfileTestdoxHtml;
}

/**
@phpstan-assert-if-true
*/
public function hasLogfileTestdoxText(): bool
{
return $this->logfileTestdoxText !== null;
}




public function logfileTestdoxText(): string
{
if (!$this->hasLogfileTestdoxText()) {
throw new LoggingNotConfiguredException;
}

return $this->logfileTestdoxText;
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
throw new LoggingNotConfiguredException;
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
throw new LoggingNotConfiguredException;
}

return $this->logEventsVerboseText;
}

public function outputIsTeamCity(): bool
{
return $this->teamCityOutput;
}

public function outputIsTestDox(): bool
{
return $this->testDoxOutput;
}

public function testDoxOutputWithSummary(): bool
{
return $this->testDoxOutputSummary;
}

/**
@phpstan-assert-if-true
*/
public function hasTestsCovering(): bool
{
return !empty($this->testsCovering);
}






public function testsCovering(): array
{
if (!$this->hasTestsCovering()) {
throw new FilterNotConfiguredException;
}

return $this->testsCovering;
}

/**
@phpstan-assert-if-true
*/
public function hasTestsUsing(): bool
{
return !empty($this->testsUsing);
}






public function testsUsing(): array
{
if (!$this->hasTestsUsing()) {
throw new FilterNotConfiguredException;
}

return $this->testsUsing;
}

/**
@phpstan-assert-if-true
*/
public function hasTestsRequiringPhpExtension(): bool
{
return !empty($this->testsRequiringPhpExtension);
}






public function testsRequiringPhpExtension(): array
{
if (!$this->hasTestsRequiringPhpExtension()) {
throw new FilterNotConfiguredException;
}

return $this->testsRequiringPhpExtension;
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
throw new FilterNotConfiguredException;
}

return $this->filter;
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
throw new FilterNotConfiguredException;
}

return $this->excludeFilter;
}

/**
@phpstan-assert-if-true
*/
public function hasGroups(): bool
{
return !empty($this->groups);
}






public function groups(): array
{
if (!$this->hasGroups()) {
throw new FilterNotConfiguredException;
}

return $this->groups;
}

/**
@phpstan-assert-if-true
*/
public function hasExcludeGroups(): bool
{
return !empty($this->excludeGroups);
}






public function excludeGroups(): array
{
if (!$this->hasExcludeGroups()) {
throw new FilterNotConfiguredException;
}

return $this->excludeGroups;
}

public function randomOrderSeed(): int
{
return $this->randomOrderSeed;
}

public function includeUncoveredFiles(): bool
{
return $this->includeUncoveredFiles;
}

public function testSuite(): TestSuiteCollection
{
return $this->testSuite;
}

public function includeTestSuite(): string
{
return $this->includeTestSuite;
}

public function excludeTestSuite(): string
{
return $this->excludeTestSuite;
}

/**
@phpstan-assert-if-true
*/
public function hasDefaultTestSuite(): bool
{
return $this->defaultTestSuite !== null;
}




public function defaultTestSuite(): string
{
if (!$this->hasDefaultTestSuite()) {
throw new NoDefaultTestSuiteException;
}

return $this->defaultTestSuite;
}




public function testSuffixes(): array
{
return $this->testSuffixes;
}

public function php(): Php
{
return $this->php;
}

public function controlGarbageCollector(): bool
{
return $this->controlGarbageCollector;
}

public function numberOfTestsBeforeGarbageCollection(): int
{
return $this->numberOfTestsBeforeGarbageCollection;
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
throw new NoBaselineException;
}

return $this->generateBaseline;
}

public function debug(): bool
{
return $this->debug;
}




public function shortenArraysForExportThreshold(): int
{
return $this->shortenArraysForExportThreshold;
}
}
