<?php

declare(strict_types=1);

use Tests\Type\Fixtures\Post;

it('covers non-existent', function (): void {
    expect(true)->toBeTrue();
})->coversClass('App\NonExistent\FakeClass');
it('covers existing', function (): void {
    expect(true)->toBeTrue();
})->coversClass(Post::class);

it('covers mixed class list', function (): void {
    expect(true)->toBeTrue();
})->coversClass(Post::class, 'App\\NonExistent\\SecondFakeClass');
it('covers mixed functions', function (): void {
    expect(true)->toBeTrue();
})->coversFunction('strlen', 'missing_test_helper');
