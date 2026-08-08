<?php

declare(strict_types=1);

namespace LocalUsesTraitOnlyKeepsDirectoryTestCase;

use Tests\Type\Fixtures\CustomTestCase;
use Tests\Type\Fixtures\HelperTrait;

use function PHPStan\Testing\assertType;

uses(HelperTrait::class);

function testTraitOnlyLocalUsesKeepsTheDirectoryTestCase(): void
{
    it('keeps the directory-bound test case when the file binds only a trait', function (): void {
        assertType(CustomTestCase::class, $this);
        assertType('string', $this->publicHelper());
        assertType('string', $this->helperMethod());
    });
}
