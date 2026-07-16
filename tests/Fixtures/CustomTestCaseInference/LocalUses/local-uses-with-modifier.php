<?php

declare(strict_types=1);

namespace LocalUsesWithModifier;

use Tests\Type\Fixtures\CustomTestCase;

use function PHPStan\Testing\assertType;

uses(CustomTestCase::class)->group('integration');

function testThisTypeFromLocalUsesWithChainedModifier(): void
{
    it('reads the bound class even when uses() has chained modifiers', function (): void {
        assertType(CustomTestCase::class, $this);
    });
}
