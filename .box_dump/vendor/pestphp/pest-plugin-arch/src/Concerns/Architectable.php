<?php

declare(strict_types=1);

namespace Pest\Arch\Concerns;

use Pest\Arch\Options\TestCaseOptions;




trait Architectable 
{



private ?TestCaseOptions $options = null;




public function arch(): TestCaseOptions
{
$options = $this->options ??= new TestCaseOptions;

return $this->options = $options;
}
}
