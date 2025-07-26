<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject;

use function strtolower;
use Exception;
use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use Throwable;

/**
@no-named-arguments


*/
final class InvocationHandler
{



private array $matchers = [];




private array $matcherMap = [];




private readonly array $configurableMethods;
private readonly bool $returnValueGeneration;




public function __construct(array $configurableMethods, bool $returnValueGeneration)
{
$this->configurableMethods = $configurableMethods;
$this->returnValueGeneration = $returnValueGeneration;
}

public function hasMatchers(): bool
{
foreach ($this->matchers as $matcher) {
if ($matcher->hasMatchers()) {
return true;
}
}

return false;
}




public function lookupMatcher(string $id): ?Matcher
{
return $this->matcherMap[$id] ?? null;
}







public function registerMatcher(string $id, Matcher $matcher): void
{
if (isset($this->matcherMap[$id])) {
throw new MatcherAlreadyRegisteredException($id);
}

$this->matcherMap[$id] = $matcher;
}

public function expects(InvocationOrder $rule): InvocationMocker
{
$matcher = new Matcher($rule);
$this->addMatcher($matcher);

return new InvocationMocker(
$this,
$matcher,
...$this->configurableMethods,
);
}





public function invoke(Invocation $invocation): mixed
{
$exception = null;
$hasReturnValue = false;
$returnValue = null;

foreach ($this->matchers as $match) {
try {
if ($match->matches($invocation)) {
$value = $match->invoked($invocation);

if (!$hasReturnValue) {
$returnValue = $value;
$hasReturnValue = true;
}
}
} catch (Exception $e) {
$exception = $e;
}
}

if ($exception !== null) {
throw $exception;
}

if ($hasReturnValue) {
return $returnValue;
}

if (!$this->returnValueGeneration) {
if (strtolower($invocation->methodName()) === '__tostring') {
return '';
}

throw new ReturnValueNotConfiguredException($invocation);
}

return $invocation->generateReturnValue();
}




public function verify(): void
{
foreach ($this->matchers as $matcher) {
$matcher->verify();
}
}

private function addMatcher(Matcher $matcher): void
{
$this->matchers[] = $matcher;
}
}
