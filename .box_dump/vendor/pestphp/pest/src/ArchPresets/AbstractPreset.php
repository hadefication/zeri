<?php

declare(strict_types=1);

namespace Pest\ArchPresets;

use Pest\Arch\Contracts\ArchExpectation;
use Pest\Expectation;




abstract class AbstractPreset 
{





protected array $expectations = [];






public function __construct(
private readonly array $userNamespaces,
) {

}






abstract public function execute(): void;






final public function ignoring(array|string $targetsOrDependencies): void
{
$this->expectations = array_map(
fn (ArchExpectation|Expectation $expectation): Expectation|ArchExpectation => $expectation instanceof ArchExpectation ? $expectation->ignoring($targetsOrDependencies) : $expectation,
$this->expectations,
);
}






final public function eachUserNamespace(callable ...$callbacks): void
{
foreach ($this->userNamespaces as $namespace) {
foreach ($callbacks as $callback) {
$this->expectations[] = $callback(expect($namespace));
}
}
}




final public function flush(): void
{
$this->expectations = [];
}
}
