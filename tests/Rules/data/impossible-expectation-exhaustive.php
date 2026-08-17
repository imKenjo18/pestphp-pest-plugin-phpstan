<?php

declare(strict_types=1);

use Tests\Type\Fixtures\Post;

it('int is not string', function (): void {
    expect(1)->toBeString();
});

it('int is not float', function (): void {
    expect(1)->toBeFloat();
});

it('int is not bool', function (): void {
    expect(1)->toBeBool();
});

it('int is not null', function (): void {
    expect(1)->toBeNull();
});

it('int is not array', function (): void {
    expect(1)->toBeArray();
});

it('int is not object', function (): void {
    expect(1)->toBeObject();
});

it('int is not callable', function (): void {
    expect(1)->toBeCallable();
});

it('int is not iterable', function (): void {
    expect(1)->toBeIterable();
});

it('string is not int', function (): void {
    expect('a')->toBeInt();
});

it('string is not float', function (): void {
    expect('a')->toBeFloat();
});

it('string is not bool', function (): void {
    expect('a')->toBeBool();
});

it('string is not array', function (): void {
    expect('a')->toBeArray();
});

it('string is not null', function (): void {
    expect('a')->toBeNull();
});

it('float is not int', function (): void {
    expect(1.5)->toBeInt();
});

it('float is not string', function (): void {
    expect(1.5)->toBeString();
});

it('bool is not int', function (): void {
    expect(true)->toBeInt();
});

it('bool is not string', function (): void {
    expect(true)->toBeString();
});

it('null is not string', function (): void {
    expect(null)->toBeString();
});

it('null is not int', function (): void {
    expect(null)->toBeInt();
});

it('null is not array', function (): void {
    expect(null)->toBeArray();
});

it('array is not string', function (): void {
    expect([])->toBeString();
});

it('array is not int', function (): void {
    expect([])->toBeInt();
});

it('array is not scalar', function (): void {
    expect([])->toBeScalar();
});

it('array is not object', function (): void {
    expect([])->toBeObject();
});

it('true is not false', function (): void {
    expect(true)->toBeFalse();
});

it('false is not true', function (): void {
    expect(false)->toBeTrue();
});

it('int is not true', function (): void {
    expect(1)->toBeTrue();
});

it('string is not false', function (): void {
    expect('a')->toBeFalse();
});

it('bool is not numeric', function (): void {
    expect(true)->toBeNumeric();
});

it('null is not numeric', function (): void {
    expect(null)->toBeNumeric();
});

it('array is not numeric', function (): void {
    expect([])->toBeNumeric();
});

it('int is not an instance', function (): void {
    expect(1)->toBeInstanceOf(stdClass::class);
});

it('string is not an instance', function (): void {
    expect('a')->toBeInstanceOf(stdClass::class);
});

it('unrelated classes never match', function (): void {
    expect(new stdClass)->toBeInstanceOf(RuntimeException::class);
});

it('unrelated class hierarchies never match', function (): void {
    expect(new Post)->toBeInstanceOf(RuntimeException::class);
});

it('mixed can be a string', function (): void {
    /** @var mixed $value */
    $value = null;
    expect($value)->toBeString();
});

it('mixed can be an int', function (): void {
    /** @var mixed $value */
    $value = null;
    expect($value)->toBeInt();
});

it('mixed can be an array', function (): void {
    /** @var mixed $value */
    $value = null;
    expect($value)->toBeArray();
});

it('mixed can be an instance', function (): void {
    /** @var mixed $value */
    $value = null;
    expect($value)->toBeInstanceOf(stdClass::class);
});

it('union may match the string branch', function (): void {
    /** @var int|string $value */
    $value = 1;
    expect($value)->toBeString();
});

it('union may match the int branch', function (): void {
    /** @var int|string $value */
    $value = 1;
    expect($value)->toBeInt();
});

it('nullable may be null', function (): void {
    /** @var string|null $value */
    $value = null;
    expect($value)->toBeNull();
});

it('nullable may be a string', function (): void {
    /** @var string|null $value */
    $value = null;
    expect($value)->toBeString();
});

it('subclass matches parent', function (): void {
    expect(new RuntimeException)->toBeInstanceOf(Exception::class);
});

it('object matches interface', function (): void {
    expect(new ArrayIterator([]))->toBeInstanceOf(Traversable::class);
});

it('non type matchers are ignored', function (): void {
    expect(1)->toBe(1);
    expect(1)->toEqual(1);
    expect('a')->toContain('a');
    expect([1])->toHaveCount(1);
});

it('numeric strings are numeric', function (): void {
    /** @var numeric-string $value */
    $value = '1';
    expect($value)->toBeNumeric();
});

it('unions with arrays may be iterable', function (): void {
    /** @var array<int>|int $value */
    $value = [1];
    expect($value)->toBeIterable();
    expect($value)->toBeArray();
});
