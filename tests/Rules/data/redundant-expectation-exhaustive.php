<?php

declare(strict_types=1);

it('string is already string', function (): void {
    expect('a')->toBeString();
});

it('int is already int', function (): void {
    expect(1)->toBeInt();
});

it('float is already float', function (): void {
    expect(1.5)->toBeFloat();
});

it('true is already true', function (): void {
    expect(true)->toBeTrue();
});

it('false is already false', function (): void {
    expect(false)->toBeFalse();
});

it('true is already bool', function (): void {
    expect(true)->toBeBool();
});

it('false is already bool', function (): void {
    expect(false)->toBeBool();
});

it('null is already null', function (): void {
    expect(null)->toBeNull();
});

it('array is already array', function (): void {
    expect([])->toBeArray();
});

it('array is already iterable', function (): void {
    expect([1])->toBeIterable();
});

it('int is already numeric', function (): void {
    expect(1)->toBeNumeric();
});

it('float is already numeric', function (): void {
    expect(1.5)->toBeNumeric();
});

it('int is already scalar', function (): void {
    expect(1)->toBeScalar();
});

it('string is already scalar', function (): void {
    expect('a')->toBeScalar();
});

it('bool is already scalar', function (): void {
    expect(true)->toBeScalar();
});

it('object is already object', function (): void {
    expect(new stdClass)->toBeObject();
});

it('instance is already that class', function (): void {
    expect(new stdClass)->toBeInstanceOf(stdClass::class);
});

it('subclass is already parent instance', function (): void {
    expect(new RuntimeException)->toBeInstanceOf(Exception::class);
});

it('declared string is already string', function (): void {
    /** @var string $value */
    $value = 'a';
    expect($value)->toBeString();
});

it('declared array is already array', function (): void {
    /** @var array<int> $value */
    $value = [1];
    expect($value)->toBeArray();
});

it('declared numeric string is already numeric', function (): void {
    /** @var numeric-string $value */
    $value = '1';
    expect($value)->toBeNumeric();
});

it('scalar union is already scalar', function (): void {
    /** @var int|string $value */
    $value = 1;
    expect($value)->toBeScalar();
});

it('narrowed value is redundant downstream', function (): void {
    /** @var int|string $value */
    $value = 1;
    expect($value)->toBeString()->toBeString();
});

it('mixed is never redundant', function (): void {
    /** @var mixed $value */
    $value = null;
    expect($value)->toBeString();
    expect($value)->toBeInt();
    expect($value)->toBeArray();
});

it('unions are not redundant', function (): void {
    /** @var int|string $value */
    $value = 1;
    expect($value)->toBeString();
    expect($value)->toBeInt();
});

it('nullable is not redundant', function (): void {
    /** @var string|null $value */
    $value = null;
    expect($value)->toBeString();
    expect($value)->toBeNull();
});

it('bool is not redundant for true', function (): void {
    /** @var bool $value */
    $value = true;
    expect($value)->toBeTrue();
    expect($value)->toBeFalse();
});

it('parent instance is not redundant for subclass', function (): void {
    /** @var Exception $value */
    $value = new RuntimeException;
    expect($value)->toBeInstanceOf(RuntimeException::class);
});

it('non type matchers are ignored', function (): void {
    expect('a')->toBe('a');
    expect(1)->toEqual(1);
    expect([1])->toHaveCount(1);
    expect('a')->toContain('a');
});

it('int is not redundant for float', function (): void {
    expect(1)->toBeFloat();
});
