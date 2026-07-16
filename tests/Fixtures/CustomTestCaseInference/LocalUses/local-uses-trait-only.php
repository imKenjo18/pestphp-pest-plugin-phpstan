<?php

declare(strict_types=1);

namespace LocalUsesTraitOnly;

use Tests\Type\Fixtures\HelperTrait;

use function PHPStan\Testing\assertType;

uses(HelperTrait::class);

function testThisTypeFallsBackToTestCaseWhenOnlyATraitIsUsed(): void
{
    it('falls back to the default TestCase $this when only a trait is used', function (): void {
        assertType(\PHPUnit\Framework\TestCase::class, $this);
    });
}
