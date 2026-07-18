<?php

declare(strict_types=1);

it('empty it', function (): void {});

test('empty test', function (): void {});

it('empty with a chain', function (): void {})->group('unit');

it('empty with only a comment', function (): void {
    //
});

it('duplicated it', function (): void {
    expect(true)->toBeTrue();
});

it('duplicated it', function (): void {
    expect(true)->toBeTrue();
});

test('duplicated test', function (): void {
    expect(true)->toBeTrue();
});

test('duplicated test', function (): void {
    expect(true)->toBeTrue();
});

it('collides with a test', function (): void {
    expect(true)->toBeTrue();
});

test('it collides with a test', function (): void {
    expect(true)->toBeTrue();
});

it('triplicated', function (): void {
    expect(true)->toBeTrue();
});

it('triplicated', function (): void {
    expect(true)->toBeTrue();
});

it('triplicated', function (): void {
    expect(true)->toBeTrue();
});

it('duplicated through a chain', function (): void {
    expect(true)->toBeTrue();
})->group('unit');

it('duplicated through a chain', function (): void {
    expect(true)->toBeTrue();
})->skip();

it('has a body', function (): void {
    expect(true)->toBeTrue();
});

it('is marked as todo', function (): void {})->todo();

test('unique description one', function (): void {
    expect(true)->toBeTrue();
});

test('unique description two', function (): void {
    expect(true)->toBeTrue();
});

it('differs from the test of the same words', function (): void {
    expect(true)->toBeTrue();
});

test('differs from the test of the same words', function (): void {
    expect(true)->toBeTrue();
});

describe('descriptions inside describe are scoped separately', function (): void {
    it('duplicated it', function (): void {
        expect(true)->toBeTrue();
    });
});
