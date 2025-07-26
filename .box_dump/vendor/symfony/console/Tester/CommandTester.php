<?php










namespace Symfony\Component\Console\Tester;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;







class CommandTester
{
use TesterTrait;

public function __construct(
private Command $command,
) {
}
















public function execute(array $input, array $options = []): int
{


if (!isset($input['command'])
&& (null !== $application = $this->command->getApplication())
&& $application->getDefinition()->hasArgument('command')
) {
$input = array_merge(['command' => $this->command->getName()], $input);
}

$this->input = new ArrayInput($input);

$this->input->setStream(self::createStream($this->inputs));

if (isset($options['interactive'])) {
$this->input->setInteractive($options['interactive']);
}

if (!isset($options['decorated'])) {
$options['decorated'] = false;
}

$this->initOutput($options);

return $this->statusCode = $this->command->run($this->input, $this->output);
}
}
