<?php

declare(strict_types=1);

$token = getenv('TEST_TOKEN');
$suffix = ($token === false || $token === '') ? '' : '-'.$token;

$base = getenv('TMPDIR') ?: (getenv('TMP') ?: (getenv('TEMP') ?: (DIRECTORY_SEPARATOR === '\\' ? 'C:\\Windows\\Temp' : '/tmp')));
$tmpDir = mb_rtrim($base, '/\\').DIRECTORY_SEPARATOR.'pest-plugin-phpstan'.$suffix;

if (! is_dir($tmpDir)) {
    @mkdir($tmpDir, 0777, true);
}

putenv('TMPDIR='.$tmpDir);
$_ENV['TMPDIR'] = $tmpDir;
$_SERVER['TMPDIR'] = $tmpDir;

require __DIR__.'/../vendor/autoload.php';
