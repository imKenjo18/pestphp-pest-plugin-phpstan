<?php

declare(strict_types=1);

it('subclass is already an instance of its parent', function (): void {
    expect(new RuntimeException)->toBeInstanceOf(Exception::class);
});

it('implementation is already an instance of its interface', function (): void {
    expect(new RuntimeException)->toBeInstanceOf(Throwable::class);
});

it('scalar unions are already scalar', function (): void {
    /** @var int|string $value */
    $value = getValue();

    expect($value)->toBeScalar();
});

it('numeric strings are already numeric', function (): void {
    /** @var numeric-string $value */
    $value = '42';

    expect($value)->toBeNumeric();
});

it('mixed object unions are not always redundant', function (): void {
    /** @var ArrayObject|stdClass $value */
    $value = getValue();

    expect($value)->toBeInstanceOf(Countable::class);
});
