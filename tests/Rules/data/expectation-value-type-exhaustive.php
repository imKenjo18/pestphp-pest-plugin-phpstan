<?php

declare(strict_types=1);

it('json requires string', function (): void {
    expect(1)->json();
});

it('toStartWith requires string', function (): void {
    expect(1)->toStartWith('a');
});

it('toEndWith requires string', function (): void {
    expect(1)->toEndWith('a');
});

it('toBeJson requires string', function (): void {
    expect(1)->toBeJson();
});

it('toBeUppercase requires string', function (): void {
    expect(1)->toBeUppercase();
});

it('toBeLowercase requires string', function (): void {
    expect(1)->toBeLowercase();
});

it('toBeAlphaNumeric requires string', function (): void {
    expect(1)->toBeAlphaNumeric();
});

it('toBeAlpha requires string', function (): void {
    expect(1)->toBeAlpha();
});

it('toBeDigits requires string', function (): void {
    expect(1)->toBeDigits();
});

it('toBeSnakeCase requires string', function (): void {
    expect(1)->toBeSnakeCase();
});

it('toBeKebabCase requires string', function (): void {
    expect(1)->toBeKebabCase();
});

it('toBeCamelCase requires string', function (): void {
    expect(1)->toBeCamelCase();
});

it('toBeStudlyCase requires string', function (): void {
    expect(1)->toBeStudlyCase();
});

it('toBeUuid requires string', function (): void {
    expect(1)->toBeUuid();
});

it('toBeUrl requires string', function (): void {
    expect(1)->toBeUrl();
});

it('toBeSlug requires string', function (): void {
    expect(1)->toBeSlug();
});

it('toMatch requires string', function (): void {
    expect(1)->toMatch('/a/');
});

it('toBeDirectory requires string', function (): void {
    expect(1)->toBeDirectory();
});

it('toBeFile requires string', function (): void {
    expect(1)->toBeFile();
});

it('toBeReadableFile requires string', function (): void {
    expect(1)->toBeReadableFile();
});

it('toBeWritableFile requires string', function (): void {
    expect(1)->toBeWritableFile();
});

it('toBeReadableDirectory requires string', function (): void {
    expect(1)->toBeReadableDirectory();
});

it('toBeWritableDirectory requires string', function (): void {
    expect(1)->toBeWritableDirectory();
});

it('string matchers reject arrays', function (): void {
    expect([])->toStartWith('a');
});

it('string matchers reject null', function (): void {
    expect(null)->toBeUppercase();
});

it('string matchers reject bool', function (): void {
    expect(true)->toMatch('/a/');
});

it('string matchers reject objects', function (): void {
    expect(new stdClass)->toBeJson();
});

it('each requires iterable', function (): void {
    expect(1)->each();
});

it('sequence requires iterable', function (): void {
    expect(1)->sequence();
});

it('toContainEqual requires iterable', function (): void {
    expect(1)->toContainEqual(1);
});

it('toContainOnlyInstancesOf requires iterable', function (): void {
    expect('a')->toContainOnlyInstancesOf(stdClass::class);
});

it('toHaveCount requires countable', function (): void {
    expect('a')->toHaveCount(1);
});

it('toHaveSameSize requires countable', function (): void {
    expect(1)->toHaveSameSize([1]);
});

it('strings satisfy string matchers', function (): void {
    expect('a')->toStartWith('a');
    expect('a')->toEndWith('a');
    expect('{"a":1}')->toBeJson();
    expect('a')->toBeUppercase();
    expect('a')->toMatch('/a/');
    expect('a')->toBeUuid();
});

it('arrays satisfy iterable matchers', function (): void {
    expect([1])->each();
    expect([1])->toContainEqual(1);
    expect([1])->toHaveCount(1);
    expect([1])->toHaveSameSize([1]);
});

it('traversables satisfy iterable matchers', function (): void {
    expect(new ArrayIterator([1]))->each();
    expect(new ArrayIterator([1]))->toHaveCount(1);
});

it('mixed satisfies everything', function (): void {
    /** @var mixed $value */
    $value = null;
    expect($value)->toStartWith('a');
    expect($value)->each();
    expect($value)->toHaveCount(1);
});

it('unions containing string are allowed', function (): void {
    /** @var int|string $value */
    $value = 'a';
    expect($value)->toStartWith('a');
});

it('narrowed chains satisfy requirements', function (): void {
    /** @var int|string $value */
    $value = 'a';
    expect($value)->toBeString()->toStartWith('a');
});

it('json narrows into an array for iterable matchers', function (): void {
    expect('{"a":1}')->json()->toHaveCount(1);
});
