<?php

declare(strict_types=1);

namespace LocalPestExtend;

use Tests\Type\Fixtures\CustomTestCase;

use function PHPStan\Testing\assertType;

pest()->extend(CustomTestCase::class);

function testThisTypeFromLocalPestExtend(): void
{
    it('has custom $this type from a file-level pest()->extend()', function (): void {
        assertType(CustomTestCase::class, $this);
    });
}
