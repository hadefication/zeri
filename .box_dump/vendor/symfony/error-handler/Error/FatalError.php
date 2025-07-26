<?php










namespace Symfony\Component\ErrorHandler\Error;

class FatalError extends \Error
{



public function __construct(
string $message,
int $code,
private array $error,
?int $traceOffset = null,
bool $traceArgs = true,
?array $trace = null,
) {
parent::__construct($message, $code);

if (null !== $trace) {
if (!$traceArgs) {
foreach ($trace as &$frame) {
unset($frame['args'], $frame['this'], $frame);
}
}
} elseif (null !== $traceOffset) {
if (\function_exists('xdebug_get_function_stack') && \in_array(\ini_get('xdebug.mode'), ['develop', false], true) && $trace = @xdebug_get_function_stack()) {
if (0 < $traceOffset) {
array_splice($trace, -$traceOffset);
}

foreach ($trace as &$frame) {
if (!isset($frame['type'])) {

if (isset($frame['class'])) {
$frame['type'] = '::';
}
} elseif ('dynamic' === $frame['type']) {
$frame['type'] = '->';
} elseif ('static' === $frame['type']) {
$frame['type'] = '::';
}


if (!$traceArgs) {
unset($frame['params'], $frame['args']);
} elseif (isset($frame['params']) && !isset($frame['args'])) {
$frame['args'] = $frame['params'];
unset($frame['params']);
}
}

unset($frame);
$trace = array_reverse($trace);
} else {
$trace = [];
}
}

foreach ([
'file' => $error['file'],
'line' => $error['line'],
'trace' => $trace,
] as $property => $value) {
if (null !== $value) {
$refl = new \ReflectionProperty(\Error::class, $property);
$refl->setValue($this, $value);
}
}
}

public function getError(): array
{
return $this->error;
}
}
