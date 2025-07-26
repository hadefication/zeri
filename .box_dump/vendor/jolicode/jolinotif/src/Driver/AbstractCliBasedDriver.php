<?php










namespace Joli\JoliNotif\Driver;

use Joli\JoliNotif\Exception\InvalidNotificationException;
use Joli\JoliNotif\Notification;
use Joli\JoliNotif\Util\PharExtractor;
use JoliCode\PhpOsHelper\OsHelper;
use Symfony\Component\Process\Process;




abstract class AbstractCliBasedDriver implements DriverInterface
{
public const SUPPORT_NONE = -1;
public const SUPPORT_UNKNOWN = 0;
public const SUPPORT_NATIVE = 1;
public const SUPPORT_BINARY_PROVIDED = 2;




private int $support = self::SUPPORT_UNKNOWN;

public function isSupported(): bool
{
if (self::SUPPORT_UNKNOWN !== $this->support) {
return self::SUPPORT_NONE !== $this->support;
}

if ($this->isBinaryAvailable()) {
$this->support = self::SUPPORT_NATIVE;

return true;
}

if ($this instanceof BinaryProviderInterface && $this->canBeUsed()) {
$this->support = self::SUPPORT_BINARY_PROVIDED;

return true;
}

$this->support = self::SUPPORT_NONE;

return false;
}

public function send(Notification $notification): bool
{
if (!$notification->getBody()) {
throw new InvalidNotificationException($notification, 'Notification body can not be empty');
}

$arguments = $this->getCommandLineArguments($notification);

if (self::SUPPORT_BINARY_PROVIDED === $this->support && $this instanceof BinaryProviderInterface) {
$dir = rtrim($this->getRootDir(), '/') . '/';
$embeddedBinary = $dir . $this->getEmbeddedBinary();

if (PharExtractor::isLocatedInsideAPhar($embeddedBinary)) {
$embeddedBinary = PharExtractor::extractFile($embeddedBinary);

foreach ($this->getExtraFiles() as $file) {
PharExtractor::extractFile($dir . $file);
}
}

$binary = $embeddedBinary;
} else {
$binary = $this->getBinary();
}

$process = new Process(array_merge([$binary], $arguments));
$this->launchProcess($process);

return $this->handleExitCode($process);
}






abstract protected function getCommandLineArguments(Notification $notification): array;




abstract protected function getBinary(): string;




protected function isBinaryAvailable(): bool
{
if (OsHelper::isUnix()) {


$process = new Process([
'sh',
'-c',
'command -v $0',
$this->getBinary(),
]);
} else {

$process = new Process([
'where',
$this->getBinary(),
]);
}

$process->run();

return $process->isSuccessful();
}

protected function launchProcess(Process $process): void
{
$process->run();
}




protected function handleExitCode(Process $process): bool
{
return 0 === $process->getExitCode();
}
}
