<?php

declare(strict_types=1);

it('each on int', function (): void {
    expect(42)->each();
});

it('each on string', function (): void {
    expect('hello')->each();
});

it('sequence on int', function (): void {
    expect(42)->sequence(fn ($item) => $item->toBe(1));
});

it('json on int', function (): void {
    expect(42)->json();
});

it('json on array', function (): void {
    expect([1, 2])->json();
});

it('toStartWith on int', function (): void {
    expect(42)->toStartWith('he');
});

it('toEndWith on int', function (): void {
    expect(42)->toEndWith('lo');
});

it('toBeJson on int', function (): void {
    expect(42)->toBeJson();
});

it('toBeFile on int', function (): void {
    expect(42)->toBeFile();
});

it('toBeDirectory on int', function (): void {
    expect(42)->toBeDirectory();
});

it('each on array', function (): void {
    expect([1, 2, 3])->each();
});

it('sequence on array', function (): void {
    expect([1, 2])->sequence(fn ($item) => $item->toBe(1));
});

it('json on string', function (): void {
    expect('{"key": "value"}')->json();
});

it('toStartWith on string', function (): void {
    expect('hello')->toStartWith('he');
});

it('toBeJson on string', function (): void {
    expect('{}')->toBeJson();
});

it('toBeFile on string', function (): void {
    expect('/tmp/file.txt')->toBeFile();
});

it('each on mixed', function (): void {
    /** @var mixed $value */
    $value = getValue();
    expect($value)->each();
});
