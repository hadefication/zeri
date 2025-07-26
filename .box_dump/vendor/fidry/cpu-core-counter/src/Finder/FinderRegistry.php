<?php










declare(strict_types=1);

namespace Fidry\CpuCoreCounter\Finder;

final class FinderRegistry
{



public static function getAllVariants(): array
{
return [
new CpuInfoFinder(),
new DummyCpuCoreFinder(1),
new HwLogicalFinder(),
new HwPhysicalFinder(),
new LscpuLogicalFinder(),
new LscpuPhysicalFinder(),
new _NProcessorFinder(),
new NProcessorFinder(),
new NProcFinder(true),
new NProcFinder(false),
new NullCpuCoreFinder(),
SkipOnOSFamilyFinder::forWindows(
new DummyCpuCoreFinder(1)
),
OnlyOnOSFamilyFinder::forWindows(
new DummyCpuCoreFinder(1)
),
new OnlyInPowerShellFinder(new CmiCmdletLogicalFinder()),
new OnlyInPowerShellFinder(new CmiCmdletPhysicalFinder()),
new WindowsRegistryLogicalFinder(),
new WmicPhysicalFinder(),
new WmicLogicalFinder(),
];
}




public static function getDefaultLogicalFinders(): array
{
return [
OnlyOnOSFamilyFinder::forWindows(
new OnlyInPowerShellFinder(
new CmiCmdletLogicalFinder()
)
),
OnlyOnOSFamilyFinder::forWindows(new WindowsRegistryLogicalFinder()),
OnlyOnOSFamilyFinder::forWindows(new WmicLogicalFinder()),
new NProcFinder(),
new HwLogicalFinder(),
new _NProcessorFinder(),
new NProcessorFinder(),
new LscpuLogicalFinder(),
new CpuInfoFinder(),
];
}




public static function getDefaultPhysicalFinders(): array
{
return [
OnlyOnOSFamilyFinder::forWindows(
new OnlyInPowerShellFinder(
new CmiCmdletPhysicalFinder()
)
),
OnlyOnOSFamilyFinder::forWindows(new WmicPhysicalFinder()),
new HwPhysicalFinder(),
new LscpuPhysicalFinder(),
];
}

private function __construct()
{
}
}
