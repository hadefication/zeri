<?php










namespace Symfony\Component\ErrorHandler;

use Psr\Log\AbstractLogger;






class BufferingLogger extends AbstractLogger
{
private array $logs = [];

public function log($level, $message, array $context = []): void
{
$this->logs[] = [$level, $message, $context];
}

public function cleanLogs(): array
{
$logs = $this->logs;
$this->logs = [];

return $logs;
}

public function __sleep(): array
{
throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
}

public function __wakeup(): void
{
throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
}

public function __destruct()
{
foreach ($this->logs as [$level, $message, $context]) {
if (str_contains($message, '{')) {
foreach ($context as $key => $val) {
if (null === $val || \is_scalar($val) || $val instanceof \Stringable) {
$message = str_replace("{{$key}}", $val, $message);
} elseif ($val instanceof \DateTimeInterface) {
$message = str_replace("{{$key}}", $val->format(\DateTimeInterface::RFC3339), $message);
} elseif (\is_object($val)) {
$message = str_replace("{{$key}}", '[object '.get_debug_type($val).']', $message);
} else {
$message = str_replace("{{$key}}", '['.\gettype($val).']', $message);
}
}
}

error_log(\sprintf('%s [%s] %s', date(\DateTimeInterface::RFC3339), $level, $message));
}
}
}
