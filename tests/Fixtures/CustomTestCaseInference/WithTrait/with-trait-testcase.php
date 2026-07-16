<?php

declare(strict_types=1);

namespace WithTraitTestCase;

use Tests\Type\Fixtures\CustomTestCase;

use function PHPStan\Testing\assertType;

function testThisTypeWhenClassAndTraitAreBound(): void
{
    it('has a concrete $this type when a class and a trait are bound', function (): void {
        assertType(CustomTestCase::class, $this);
    });
}
