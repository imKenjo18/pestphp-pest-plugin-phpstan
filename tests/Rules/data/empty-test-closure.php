<?php

declare(strict_types=1);

it('empty it closure', function (): void {});

test('empty test closure', function (): void {});

it('has assertions', function (): void {
    expect(true)->toBeTrue();
});

test('has assertions', function (): void {
    expect(42)->toBeInt();
});

todo('implement later');

test('without closure');
