<?php

declare(strict_types=1);

namespace WithTraitMethodCall;

use function PHPStan\Testing\assertType;

function testTraitMethodCallableOnThis(): void
{
    it('can call trait methods on $this', function (): void {
        assertType('string', $this->helperMethod());
    });
}

function testTraitPropertyAccessibleOnThis(): void
{
    it('has trait methods in the $this type', function (): void {
        assertType('string', $this->helperMethod());
    });
}
