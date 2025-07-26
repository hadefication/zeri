<?php

declare(strict_types=1);

namespace NunoMaduro\Collision;

use Symfony\Component\Console\Output\OutputInterface;
use Whoops\Handler\Handler as AbstractHandler;






final class Handler extends AbstractHandler
{



private Writer $writer;




public function __construct(?Writer $writer = null)
{
$this->writer = $writer ?: new Writer;
}




public function handle(): int
{
$this->writer->write($this->getInspector()); 

return self::QUIT;
}




public function setOutput(OutputInterface $output): self
{
$this->writer->setOutput($output);

return $this;
}




public function getWriter(): Writer
{
return $this->writer;
}
}
