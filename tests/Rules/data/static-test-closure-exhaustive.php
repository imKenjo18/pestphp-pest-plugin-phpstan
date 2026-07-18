<?php

declare(strict_types=1);

it('static it', static function (): void {
    expect(true)->toBeTrue();
});

test('static test', static function (): void {
    expect(true)->toBeTrue();
});

describe('static describe', static function (): void {
    it('inner', function (): void {});
});

beforeEach(static function (): void {});

afterEach(static function (): void {});

beforeAll(static function (): void {});

afterAll(static function (): void {});

it('static with chain', static function (): void {})->group('unit');

it('static with dataset', static function (int $value): void {})->with([1, 2]);

it('static arrow-bodied', static function (): void {
    expect(1)->toBe(1);
})->skip();

it('regular it', function (): void {
    expect(true)->toBeTrue();
});

test('regular test', function (): void {
    expect(true)->toBeTrue();
});

describe('regular describe', function (): void {
    it('inner', function (): void {
        expect(true)->toBeTrue();
    });
});

beforeEach(function (): void {});

afterEach(function (): void {});

beforeAll(function (): void {});

afterAll(function (): void {});

it('regular with chain', function (): void {})->group('unit');

$callback = static fn (): int => 1;

it('uses a static callback internally', function () use ($callback): void {
    expect($callback())->toBe(1);
});
