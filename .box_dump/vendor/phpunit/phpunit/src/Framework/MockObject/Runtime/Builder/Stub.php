<?php declare(strict_types=1);








namespace PHPUnit\Framework\MockObject\Builder;

use PHPUnit\Framework\MockObject\Stub\Stub as BaseStub;

/**
@no-named-arguments


*/
interface Stub extends Identity
{




public function will(BaseStub $stub): Identity;
}
