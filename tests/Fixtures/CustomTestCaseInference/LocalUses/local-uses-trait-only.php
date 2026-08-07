<?php

declare(strict_types=1);

namespace LocalUsesTraitOnly;

use Tests\Type\Fixtures\HelperTrait;

use function PHPStan\Testing\assertType;

uses(HelperTrait::class);

function testThisTypeWhenOnlyATraitIsUsed(): void
{
    it('falls back to the default TestCase $this and exposes trait methods', function (): void {
        assertType(\PHPUnit\Framework\TestCase::class, $this);
        assertType('string', $this->helperMethod());
    });
}
