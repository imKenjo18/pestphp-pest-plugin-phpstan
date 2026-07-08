<?php

declare(strict_types=1);

it('toBeUppercase on int', function (): void {
    expect(42)->toBeUppercase();
});

it('toBeLowercase on int', function (): void {
    expect(42)->toBeLowercase();
});

it('toBeAlphaNumeric on int', function (): void {
    expect(42)->toBeAlphaNumeric();
});

it('toBeAlpha on int', function (): void {
    expect(42)->toBeAlpha();
});

it('toBeSnakeCase on int', function (): void {
    expect(42)->toBeSnakeCase();
});

it('toBeKebabCase on int', function (): void {
    expect(42)->toBeKebabCase();
});

it('toBeCamelCase on int', function (): void {
    expect(42)->toBeCamelCase();
});

it('toBeStudlyCase on int', function (): void {
    expect(42)->toBeStudlyCase();
});

it('toBeUuid on int', function (): void {
    expect(42)->toBeUuid();
});

it('toBeUrl on int', function (): void {
    expect(42)->toBeUrl();
});

it('toBeSlug on int', function (): void {
    expect(42)->toBeSlug();
});

it('string matchers on strings', function (): void {
    expect('ABC')->toBeUppercase();
    expect('abc')->toBeLowercase();
    expect('abc123')->toBeAlphaNumeric();
    expect('abc')->toBeAlpha();
    expect('snake_case')->toBeSnakeCase();
    expect('kebab-case')->toBeKebabCase();
    expect('camelCase')->toBeCamelCase();
    expect('StudlyCase')->toBeStudlyCase();
    expect('550e8400-e29b-41d4-a716-446655440000')->toBeUuid();
    expect('https://pestphp.com')->toBeUrl();
    expect('some title')->toBeSlug();
});
