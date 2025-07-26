<?php declare(strict_types=1);








namespace SebastianBergmann\Environment;

use const PHP_BINARY;
use const PHP_SAPI;
use const PHP_VERSION;
use function array_map;
use function array_merge;
use function assert;
use function escapeshellarg;
use function explode;
use function extension_loaded;
use function in_array;
use function ini_get;
use function is_array;
use function parse_ini_file;
use function php_ini_loaded_file;
use function php_ini_scanned_files;
use function phpversion;
use function sprintf;
use function strrpos;
use function version_compare;
use function xdebug_info;

final class Runtime
{




public function canCollectCodeCoverage(): bool
{
if ($this->hasPHPDBGCodeCoverage()) {
return true;
}

if ($this->hasPCOV()) {
return true;
}

if (!$this->hasXdebug()) {
return false;
}

$xdebugVersion = phpversion('xdebug');

assert($xdebugVersion !== false);

if (version_compare($xdebugVersion, '3', '<')) {
return true;
}

$xdebugMode = xdebug_info('mode');

assert(is_array($xdebugMode));

if (in_array('coverage', $xdebugMode, true)) {
return true;
}

return false;
}





public function discardsComments(): bool
{
if (!$this->isOpcacheActive()) {
return false;
}

if (ini_get('opcache.save_comments') !== '0') {
return false;
}

return true;
}





public function performsJustInTimeCompilation(): bool
{
if (!$this->isOpcacheActive()) {
return false;
}

if (ini_get('opcache.jit_buffer_size') === '0') {
return false;
}

$jit = (string) ini_get('opcache.jit');

if (($jit === 'disable') || ($jit === 'off')) {
return false;
}

if (strrpos($jit, '0') === 3) {
return false;
}

return true;
}






public function getRawBinary(): string
{
return PHP_BINARY;
}






public function getBinary(): string
{
return escapeshellarg(PHP_BINARY);
}

public function getNameWithVersion(): string
{
return $this->getName() . ' ' . $this->getVersion();
}

public function getNameWithVersionAndCodeCoverageDriver(): string
{
if ($this->hasPCOV()) {
return sprintf(
'%s with PCOV %s',
$this->getNameWithVersion(),
phpversion('pcov'),
);
}

if ($this->hasXdebug()) {
return sprintf(
'%s with Xdebug %s',
$this->getNameWithVersion(),
phpversion('xdebug'),
);
}

return $this->getNameWithVersion();
}

public function getName(): string
{
if ($this->isPHPDBG()) {

return 'PHPDBG';

}

return 'PHP';
}

public function getVendorUrl(): string
{
return 'https://www.php.net/';
}

public function getVersion(): string
{
return PHP_VERSION;
}




public function hasXdebug(): bool
{
return $this->isPHP() && extension_loaded('xdebug');
}




public function isPHP(): bool
{
return !$this->isPHPDBG();
}




public function isPHPDBG(): bool
{
return PHP_SAPI === 'phpdbg';
}





public function hasPHPDBGCodeCoverage(): bool
{
return $this->isPHPDBG();
}




public function hasPCOV(): bool
{
return $this->isPHP() && extension_loaded('pcov') && ini_get('pcov.enabled');
}















public function getCurrentSettings(array $values): array
{
$diff = [];
$files = [];

if ($file = php_ini_loaded_file()) {
$files[] = $file;
}

if ($scanned = php_ini_scanned_files()) {
$files = array_merge(
$files,
array_map(
'trim',
explode(",\n", $scanned),
),
);
}

foreach ($files as $ini) {
$config = parse_ini_file($ini, true);

foreach ($values as $value) {
$set = ini_get($value);

if (empty($set)) {
continue;
}

if ((!isset($config[$value]) || ($set !== $config[$value]))) {
$diff[$value] = sprintf('%s=%s', $value, $set);
}
}
}

return $diff;
}

private function isOpcacheActive(): bool
{
if (!extension_loaded('Zend OPcache')) {
return false;
}

if ((PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') && ini_get('opcache.enable_cli') === '1') {
return true;
}

if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg' && ini_get('opcache.enable') === '1') {
return true;
}

return false;
}
}
