<?php

declare(strict_types=1);

it('int cannot be string', function (): void {
    expect(42)->toBeString();
});

it('string cannot be int', function (): void {
    expect('hello')->toBeInt();
});

it('string cannot be float', function (): void {
    expect('hello')->toBeFloat();
});

it('string cannot be bool', function (): void {
    expect('hello')->toBeBool();
});

it('int cannot be true', function (): void {
    expect(42)->toBeTrue();
});

it('int cannot be false', function (): void {
    expect(42)->toBeFalse();
});

it('string cannot be null', function (): void {
    expect('hello')->toBeNull();
});

it('string cannot be array', function (): void {
    expect('hello')->toBeArray();
});

it('int cannot be object', function (): void {
    expect(42)->toBeObject();
});

it('int cannot be iterable', function (): void {
    expect(42)->toBeIterable();
});

it('null cannot be callable', function (): void {
    expect()->toBeCallable();
});

it('int cannot be instance of stdClass', function (): void {
    expect(42)->toBeInstanceOf(stdClass::class);
});

it('array cannot be scalar', function (): void {
    expect([])->toBeScalar();
});

it('null cannot be numeric', function (): void {
    expect()->toBeNumeric();
});

it('string is string', function (): void {
    expect('hello')->toBeString();
});

it('int is int', function (): void {
    expect(42)->toBeInt();
});

it('mixed could be anything', function (): void {
    /** @var mixed $value */
    $value = getValue();
    expect($value)->toBeString()->toBeInt();
});

it('union type might match', function (): void {
    /** @var int|string $value */
    $value = getValue();
    expect($value)->toBeString()->toBeInt();
});

it('true is bool', function (): void {
    expect(true)->toBeBool();
});

it('null is null', function (): void {
    expect()->toBeNull();
});

it('array is array', function (): void {
    expect([1, 2])->toBeArray();
});

it('object is object', function (): void {
    expect(new stdClass)->toBeObject();
});

it('array is iterable', function (): void {
    expect([1, 2])->toBeIterable();
});

it('int is scalar', function (): void {
    expect(42)->toBeScalar();
});

it('int is numeric', function (): void {
    expect(42)->toBeNumeric();
});
