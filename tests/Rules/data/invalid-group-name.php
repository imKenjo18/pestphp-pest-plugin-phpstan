<?php

declare(strict_types=1);

it('empty group name', function (): void {
    expect(true)->toBeTrue();
})->group('');

it('whitespace group name', function (): void {
    expect(true)->toBeTrue();
})->group('  ');

it('valid group name', function (): void {
    expect(true)->toBeTrue();
})->group('feature');

it('multiple groups', function (): void {
    expect(true)->toBeTrue();
})->group('feature', 'integration');
