<?php

declare(strict_types=1);

namespace LocalUsesBaseAndTrait;

use Tests\Type\Fixtures\CustomTestCase;
use Tests\Type\Fixtures\HelperTrait;

use function PHPStan\Testing\assertType;

uses(CustomTestCase::class, HelperTrait::class);

function testThisTypeFromLocalUsesWithClassAndTrait(): void
{
    it('has the bound class as $this when a class and a trait are used together', function (): void {
        assertType(CustomTestCase::class, $this);
    });
}
