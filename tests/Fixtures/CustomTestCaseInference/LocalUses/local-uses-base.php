<?php

declare(strict_types=1);

namespace LocalUsesBase;

use Tests\Type\Fixtures\CustomTestCase;

use function PHPStan\Testing\assertType;

uses(CustomTestCase::class);

function testThisTypeFromLocalUses(): void
{
    it('has custom $this type from a file-level uses()', function (): void {
        assertType(CustomTestCase::class, $this);
    });
}

function testBeforeEachThisTypeFromLocalUses(): void
{
    beforeEach(function (): void {
        assertType(CustomTestCase::class, $this);
    });
}
