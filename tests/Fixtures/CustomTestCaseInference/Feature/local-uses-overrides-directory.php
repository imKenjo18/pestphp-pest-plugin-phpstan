<?php

declare(strict_types=1);

namespace LocalUsesOverridesDirectory;

use Tests\Type\Fixtures\AnotherTestCase;

use function PHPStan\Testing\assertType;

uses(AnotherTestCase::class);

function testFileLevelUsesOverridesDirectoryBinding(): void
{
    it('prefers the file-level uses() over the directory binding from Pest.php', function (): void {
        assertType(AnotherTestCase::class, $this);
    });
}
