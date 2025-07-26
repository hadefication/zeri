<?php










namespace Symfony\Component\ErrorHandler\ErrorEnhancer;

interface ErrorEnhancerInterface
{



public function enhance(\Throwable $error): ?\Throwable;
}
