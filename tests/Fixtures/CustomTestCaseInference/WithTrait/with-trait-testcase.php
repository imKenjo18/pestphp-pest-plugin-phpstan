<?php

declare(strict_types=1);

namespace WithTraitTestCase;

use Tests\Type\Fixtures\CustomTestCase;
use Tests\Type\Fixtures\Post;

use function PHPStan\Testing\assertType;

function testThisTypeWhenClassAndTraitAreBound(): void
{
    it('has a concrete $this type when a class and a trait are bound', function (): void {
        assertType(CustomTestCase::class, $this);
    });
}

function testHookPropertyScopedToInTarget(): void
{
    it('types properties assigned in the config beforeEach hook bound to this directory', function (): void {
        assertType(Post::class, $this->sharedPost);
    });
}
