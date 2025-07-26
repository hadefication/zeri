<?php

declare(strict_types=1);

namespace Pest\ArchPresets;




final class Security extends AbstractPreset
{



public function execute(): void
{
$this->expectations[] = expect([
'md5',
'sha1',
'uniqid',
'rand',
'mt_rand',
'tempnam',
'str_shuffle',
'shuffle',
'array_rand',
'eval',
'exec',
'shell_exec',
'system',
'passthru',
'create_function',
'unserialize',
'extract',
'parse_str',
'mb_parse_str',
'dl',
'assert',
])->not->toBeUsed();
}
}
