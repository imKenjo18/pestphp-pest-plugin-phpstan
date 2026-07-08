<?php

declare(strict_types=1);

it('repeat zero', function (): void {
    expect(true)->toBeTrue();
})->repeat(0);
it('repeat negative', function (): void {
    expect(true)->toBeTrue();
})->repeat(-1);
it('repeat once', function (): void {
    expect(true)->toBeTrue();
})->repeat(1);

it('repeat three times', function (): void {
    expect(true)->toBeTrue();
})->repeat(3);

it('repeat hundred', function (): void {
    expect(true)->toBeTrue();
})->repeat(100);
