<?php

declare(strict_types=1);

namespace Pest\Factories;

use Closure;
use Pest\Evaluators\Attributes;
use Pest\Exceptions\ShouldNotHappen;
use Pest\Factories\Concerns\HigherOrderable;
use Pest\Repositories\DatasetsRepository;
use Pest\Support\Str;
use Pest\TestSuite;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;




final class TestCaseMethodFactory
{
use HigherOrderable;






public array $attributes = [];






public array $describing = [];




public ?string $description = null;




public int $repetitions = 1;




public bool $todo = false;






public array $issues = [];






public array $assignees = [];






public array $prs = [];






public array $notes = [];






public array $datasets = [];






public array $depends = [];






public array $groups = [];




public bool $__ran = false;




public function __construct(
public string $filename,
public ?Closure $closure,
) {
$this->closure ??= function (): void {
(Assert::getCount() > 0 || $this->doesNotPerformAssertions()) ?: self::markTestIncomplete(); 
};

$this->bootHigherOrderable();
}




public function setUp(TestCase $concrete): void
{
$concrete::flush(); 

if ($this->description === null) {
throw ShouldNotHappen::fromMessage('Description can not be empty.');
}

$testCase = TestSuite::getInstance()->tests->get($this->filename);

assert($testCase instanceof TestCaseFactory);
$testCase->factoryProxies->proxy($concrete);
$this->factoryProxies->proxy($concrete);
}




public function tearDown(TestCase $concrete): void
{
$concrete::flush(); 
}




public function getClosure(): Closure
{
$closure = $this->closure;
$testCase = TestSuite::getInstance()->tests->get($this->filename);
assert($testCase instanceof TestCaseFactory);
$method = $this;

return function (...$arguments) use ($testCase, $method, $closure): mixed {

$testCase->proxies->proxy($this);
$method->proxies->proxy($this);

$testCase->chains->chain($this);
$method->chains->chain($this);

$this->__ran = true;

return \Pest\Support\Closure::bind($closure, $this, self::class)(...$arguments);
};
}




public function receivesArguments(): bool
{
return $this->datasets !== [] || $this->depends !== [] || $this->repetitions > 1;
}




public function buildForEvaluation(): string
{
if ($this->description === null) {
throw ShouldNotHappen::fromMessage('The test description may not be empty.');
}

$methodName = Str::evaluable($this->description);

$datasetsCode = '';

$this->attributes = [
new Attribute(
\PHPUnit\Framework\Attributes\Test::class,
[],
),
new Attribute(
\PHPUnit\Framework\Attributes\TestDox::class,
[str_replace('*/', '{@*}', $this->description)],
),
...$this->attributes,
];

foreach ($this->depends as $depend) {
$depend = Str::evaluable($this->describing === [] ? $depend : Str::describe($this->describing, $depend));

$this->attributes[] = new Attribute(
\PHPUnit\Framework\Attributes\Depends::class,
[$depend],
);
}

if ($this->datasets !== [] || $this->repetitions > 1) {
$dataProviderName = $methodName.'_dataset';
$this->attributes[] = new Attribute(
DataProvider::class,
[$dataProviderName],
);
$datasetsCode = $this->buildDatasetForEvaluation($methodName, $dataProviderName);
}

$attributesCode = Attributes::code($this->attributes);

return <<<PHP
            $attributesCode
                public function $methodName(...\$arguments)
                {
                    return \$this->__runTest(
                        \$this->__test,
                        ...\$arguments,
                    );
                }
            $datasetsCode
            PHP;
}




private function buildDatasetForEvaluation(string $methodName, string $dataProviderName): string
{
$datasets = $this->datasets;

if ($this->repetitions > 1) {
$datasets = [range(1, $this->repetitions), ...$datasets];
}

DatasetsRepository::with($this->filename, $methodName, $datasets);

return <<<EOF

                public static function $dataProviderName()
                {
                    return __PestDatasets::get(self::\$__filename, "$methodName");
                }

        EOF;
}
}
