<?php

declare(strict_types=1);

use Pest\Mixins\Expectation;

it('static closure in it', static function (): void {
    expect(true)->toBeTrue();
});

test('static closure in test', static function (): void {
    expect(true)->toBeTrue();
});

describe('static closure in describe', static function (): void {
    it('inner test', function (): void {
        expect(true)->toBeTrue();
    });
});

beforeEach(static function (): void { //
});

afterEach(static function (): void { //
});

beforeAll(static function (): void { //
});

afterAll(static function (): void { //
});

it('static arrow fn in it', static fn (): Expectation => expect(true)->toBeTrue());
it('non-static closure', function (): void {
    expect(true)->toBeTrue();
});

test('non-static closure', function (): void {
    expect(true)->toBeTrue();
});

beforeEach(function (): void {
    //
});

afterEach(function (): void {
    //
});

beforeAll(function (): void {
    //
});

afterAll(function (): void {
    //
});
