<?php declare(strict_types=1);








namespace PHPUnit\TextUI\Command;

use function version_compare;
use PHPUnit\Runner\Version;

/**
@no-named-arguments


*/
final readonly class AtLeastVersionCommand implements Command
{
private string $version;

public function __construct(string $version)
{
$this->version = $version;
}

public function execute(): Result
{
if (version_compare(Version::id(), $this->version, '>=')) {
return Result::from();
}

return Result::from('', Result::FAILURE);
}
}
