<?php










namespace Joli\JoliNotif\Util;

use JoliCode\PhpOsHelper\OsHelper as BaseOsHelper;

trigger_deprecation('jolicode/jolinotif', '2.6', 'The "%s" class is deprecated and will be removed in 3.0. Use "%s" from jolicode/php-os-helper instead.', OsHelper::class, BaseOsHelper::class);




class OsHelper extends BaseOsHelper
{
}
