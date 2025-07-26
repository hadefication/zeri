<?php











declare(strict_types=1);

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Converter\TimeConverterInterface;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Provider\TimeProviderInterface;




class TimeGeneratorFactory
{
public function __construct(
private NodeProviderInterface $nodeProvider,
private TimeConverterInterface $timeConverter,
private TimeProviderInterface $timeProvider,
) {
}




public function getGenerator(): TimeGeneratorInterface
{
return new DefaultTimeGenerator($this->nodeProvider, $this->timeConverter, $this->timeProvider);
}
}
