<?php





class GlobalFunctionFile extends FactoryFile
{



private $functions;

public function __construct($file)
{
parent::__construct($file, '    ');
$this->functions = '';
}

public function addCall(FactoryCall $call)
{
$this->functions .= "\n" . $this->generateFactoryCall($call);
}

public function build()
{
$this->addFileHeader();
$this->addPart('functions_imports');
$this->addPart('functions_header');
$this->addCode($this->functions);
$this->addPart('functions_footer');
}

public function generateFactoryCall(FactoryCall $call)
{
$code = "if (!function_exists('{$call->getName()}')) {\n";
$code.= parent::generateFactoryCall($call);
$code.= "}\n";

return $code;
}
}
