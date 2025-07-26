<?php










namespace Joli\JoliNotif;

use Joli\JoliNotif\Driver\DriverInterface;

trigger_deprecation('jolicode/jolinotif', '2.7', 'The "%s" class is deprecated and will be removed in 3.0. Use %s', LegacyNotifier::class, DefaultNotifier::class);




class LegacyNotifier extends DefaultNotifier implements Notifier
{

public function __construct(array $drivers)
{
parent::__construct(null, $drivers, true);

$this->loadDriver();
}

public function isSupported(): bool
{
return true;
}

public function getPriority(): int
{
return Notifier::PRIORITY_HIGH;
}
}
