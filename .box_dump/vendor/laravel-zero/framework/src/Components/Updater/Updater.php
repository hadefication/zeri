<?php

declare(strict_types=1);










namespace LaravelZero\Framework\Components\Updater;

use Humbug\SelfUpdate\Updater as PharUpdater;
use Illuminate\Console\OutputStyle;




final class Updater
{





private $updater;




public function __construct(PharUpdater $updater)
{
$this->updater = $updater;
}

public function update(OutputStyle $output): void
{
$result = $this->updater->update();

if ($result) {
$output->success(sprintf('Updated from version %s to %s.', $this->updater->getOldVersion(),
$this->updater->getNewVersion()));
exit(0);
} elseif (! $this->updater->getNewVersion()) {
$output->success('There are no stable versions available.');
} else {
$output->success('You have the latest version installed.');
}
}
}
