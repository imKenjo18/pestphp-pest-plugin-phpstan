<?php

declare(strict_types=1);

describe('migrations', function (): void {
    test('can rollback migrations', function (): void {
        expect(true)->toBeTrue();
    });
})->skip();

describe('user settings', function (): void {
    test('receives the dataset value', function (int $value): void {
        expect($value)->toBeGreaterThan(0);
    });
})->with([10, 20]);

describe('auth', function (): void {
    beforeEach(fn () => null);

    test('can logout', function (): void {
        expect(true)->toBeTrue();
    });
})->skip(fn (): bool => true, 'only runs on sqlite')->with([10, 20])->group('slow');

describe('teardown', function (): void {
    test('example', function (): void {
        expect(true)->toBeTrue();
    });
})->after(function (): void {});
