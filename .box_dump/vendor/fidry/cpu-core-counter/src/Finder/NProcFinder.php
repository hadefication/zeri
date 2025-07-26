<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;

use Fidry\CpuCoreCounter\Executor\ProcessExecutor;
use function sprintf;







final class NProcFinder extends ProcOpenBasedFinder
{



private $all;







public function __construct(
bool $all = false,
?ProcessExecutor $executor = null
) {
parent::__construct($executor);

$this->all = $all;
}

public function toString(): string
{
return sprintf(
'NProcFinder(all=%s)',
$this->all ? 'true' : 'false'
);
}

protected function getCommand(): string
{
return 'nproc'.($this->all ? ' --all' : '');
}
}
