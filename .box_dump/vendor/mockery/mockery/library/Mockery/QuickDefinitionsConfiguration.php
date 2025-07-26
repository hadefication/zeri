<?php









namespace Mockery;

class QuickDefinitionsConfiguration
{
private const QUICK_DEFINITIONS_MODE_DEFAULT_EXPECTATION = 'QUICK_DEFINITIONS_MODE_DEFAULT_EXPECTATION';

private const QUICK_DEFINITIONS_MODE_MOCK_AT_LEAST_ONCE = 'QUICK_DEFINITIONS_MODE_MOCK_AT_LEAST_ONCE';











protected $_quickDefinitionsApplicationMode = self::QUICK_DEFINITIONS_MODE_DEFAULT_EXPECTATION;







public function shouldBeCalledAtLeastOnce(?bool $newValue = null): bool
{
if ($newValue !== null) {
$this->_quickDefinitionsApplicationMode = $newValue
? self::QUICK_DEFINITIONS_MODE_MOCK_AT_LEAST_ONCE
: self::QUICK_DEFINITIONS_MODE_DEFAULT_EXPECTATION;
}

return $this->_quickDefinitionsApplicationMode === self::QUICK_DEFINITIONS_MODE_MOCK_AT_LEAST_ONCE;
}
}
