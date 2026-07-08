<?php

declare(strict_types=1);

it('true is always toBeTrue', function (): void {
    expect(true)->toBeTrue();
});

it('false is always toBeFalse', function (): void {
    expect(false)->toBeFalse();
});

it('true is always toBeBool', function (): void {
    expect(true)->toBeBool();
});

it('string literal is always toBeString', function (): void {
    expect('hello')->toBeString();
});

it('int literal is always toBeInt', function (): void {
    expect(42)->toBeInt();
});

it('float literal is always toBeFloat', function (): void {
    expect(3.14)->toBeFloat();
});

it('null is always toBeNull', function (): void {
    expect()->toBeNull();
});

it('array is always toBeArray', function (): void {
    expect([])->toBeArray();
});

it('string is always toBeScalar', function (): void {
    expect('hello')->toBeScalar();
});

it('int is always toBeNumeric', function (): void {
    expect(42)->toBeNumeric();
});

it('stdClass instance is always toBeInstanceOf stdClass', function (): void {
    expect(new stdClass)->toBeInstanceOf(stdClass::class);
});

it('mixed could be anything', function (): void {
    /** @var mixed $value */
    $value = getValue();
    expect($value)->toBeTrue();
});

it('union type might not match', function (): void {
    /** @var int|string $value */
    $value = getValue();
    expect($value)->toBeInt();
});

it('bool might not be true', function (): void {
    /** @var bool $value */
    $value = getValue();
    expect($value)->toBeTrue();
});
