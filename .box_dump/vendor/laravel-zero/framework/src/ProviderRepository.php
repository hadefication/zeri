<?php

declare(strict_types=1);










namespace LaravelZero\Framework;

use Illuminate\Foundation\ProviderRepository as BaseProviderRepository;

use function array_merge;




final class ProviderRepository extends BaseProviderRepository
{



public function writeManifest($manifest): array
{
return array_merge(['when' => []], $manifest);
}
}
