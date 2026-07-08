<?php

declare(strict_types=1);

it('toContainEqual on int', function (): void {
    expect(42)->toContainEqual(1);
});

it('toContainOnlyInstancesOf on string', function (): void {
    expect('hello')->toContainOnlyInstancesOf(stdClass::class);
});

it('toBeDigits on int', function (): void {
    expect(42)->toBeDigits();
});

it('toMatch on int', function (): void {
    expect(42)->toMatch('/\\d+/');
});

it('iterable and string-only matchers on compatible values', function (): void {
    expect([1, 2])->toContainEqual(1);
    expect([new stdClass, new stdClass])->toContainOnlyInstancesOf(stdClass::class);
    expect('123')->toBeDigits();
    expect('hello')->toMatch('/^he/');
});
