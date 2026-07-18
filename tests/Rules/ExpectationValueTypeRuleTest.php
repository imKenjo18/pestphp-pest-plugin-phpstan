<?php

declare(strict_types=1);

namespace Tests\Rules;

use Pest\PHPStan\Rules\ExpectationValueTypeRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
    RuleTestCase::$rule = RuleTestCase::resolveRule(ExpectationValueTypeRule::class);
});

test('expectation value type mismatches are reported', function (): void {
    $this->analyse([
        __DIR__.'/data/expectation-value-type.php',
    ], [
        [
            'Calling each() on Expectation<int>; matcher requires iterable.',
            6,
            'Pass an iterable value to expect() before calling each().',
        ],
        [
            'Calling each() on Expectation<string>; matcher requires iterable.',
            10,
            'Pass an iterable value to expect() before calling each().',
        ],
        [
            'Calling sequence() on Expectation<int>; matcher requires iterable.',
            14,
            'Pass an iterable value to expect() before calling sequence().',
        ],
        [
            'Calling json() on Expectation<int>; matcher requires string.',
            18,
            'Pass a string value to expect() before calling json().',
        ],
        [
            'Calling json() on Expectation<array<int, int>>; matcher requires string.',
            22,
            'Pass a string value to expect() before calling json().',
        ],
        [
            'Calling toStartWith() on Expectation<int>; matcher requires string.',
            26,
            'Pass a string value to expect() before calling toStartWith().',
        ],
        [
            'Calling toEndWith() on Expectation<int>; matcher requires string.',
            30,
            'Pass a string value to expect() before calling toEndWith().',
        ],
        [
            'Calling toBeJson() on Expectation<int>; matcher requires string.',
            34,
            'Pass a string value to expect() before calling toBeJson().',
        ],
        [
            'Calling toBeFile() on Expectation<int>; matcher requires string.',
            38,
            'Pass a string value to expect() before calling toBeFile().',
        ],
        [
            'Calling toBeDirectory() on Expectation<int>; matcher requires string.',
            42,
            'Pass a string value to expect() before calling toBeDirectory().',
        ],
    ]);
});

test('rector-pest string matchers require string expectation values', function (): void {
    $this->analyse([
        __DIR__.'/data/expectation-modern-string-matchers.php',
    ], [
        [
            'Calling toBeUppercase() on Expectation<int>; matcher requires string.',
            6,
            'Pass a string value to expect() before calling toBeUppercase().',
        ],
        [
            'Calling toBeLowercase() on Expectation<int>; matcher requires string.',
            10,
            'Pass a string value to expect() before calling toBeLowercase().',
        ],
        [
            'Calling toBeAlphaNumeric() on Expectation<int>; matcher requires string.',
            14,
            'Pass a string value to expect() before calling toBeAlphaNumeric().',
        ],
        [
            'Calling toBeAlpha() on Expectation<int>; matcher requires string.',
            18,
            'Pass a string value to expect() before calling toBeAlpha().',
        ],
        [
            'Calling toBeSnakeCase() on Expectation<int>; matcher requires string.',
            22,
            'Pass a string value to expect() before calling toBeSnakeCase().',
        ],
        [
            'Calling toBeKebabCase() on Expectation<int>; matcher requires string.',
            26,
            'Pass a string value to expect() before calling toBeKebabCase().',
        ],
        [
            'Calling toBeCamelCase() on Expectation<int>; matcher requires string.',
            30,
            'Pass a string value to expect() before calling toBeCamelCase().',
        ],
        [
            'Calling toBeStudlyCase() on Expectation<int>; matcher requires string.',
            34,
            'Pass a string value to expect() before calling toBeStudlyCase().',
        ],
        [
            'Calling toBeUuid() on Expectation<int>; matcher requires string.',
            38,
            'Pass a string value to expect() before calling toBeUuid().',
        ],
        [
            'Calling toBeUrl() on Expectation<int>; matcher requires string.',
            42,
            'Pass a string value to expect() before calling toBeUrl().',
        ],
        [
            'Calling toBeSlug() on Expectation<int>; matcher requires string.',
            46,
            'Pass a string value to expect() before calling toBeSlug().',
        ],
    ]);
});

test('additional expectation matchers enforce proven value types', function (): void {
    $this->analyse([
        __DIR__.'/data/expectation-additional-value-type.php',
    ], [
        [
            'Calling toContainEqual() on Expectation<int>; matcher requires iterable.',
            6,
            'Pass an iterable value to expect() before calling toContainEqual().',
        ],
        [
            'Calling toContainOnlyInstancesOf() on Expectation<string>; matcher requires iterable.',
            10,
            'Pass an iterable value to expect() before calling toContainOnlyInstancesOf().',
        ],
        [
            'Calling toBeDigits() on Expectation<int>; matcher requires string.',
            14,
            'Pass a string value to expect() before calling toBeDigits().',
        ],
        [
            'Calling toMatch() on Expectation<int>; matcher requires string.',
            18,
            'Pass a string value to expect() before calling toMatch().',
        ],
    ]);
});

test('countable and chain-aware matcher requirements are reported', function (): void {
    $this->analyse([
        __DIR__.'/data/expectation-countable-analysis.php',
    ], [
        [
            'Calling toHaveCount() on Expectation<string>; matcher requires countable or iterable.',
            9,
            'Pass a countable or iterable value to expect() before calling toHaveCount().',
        ],
        [
            'Calling toHaveSameSize() on Expectation<int>; matcher requires countable or iterable.',
            13,
            'Pass a countable or iterable value to expect() before calling toHaveSameSize().',
        ],
        [
            'Calling each() on Expectation<int>; matcher requires iterable.',
            17,
            'Pass an iterable value to expect() before calling each().',
        ],
        [
            'Calling sequence() on Expectation<int>; matcher requires iterable.',
            21,
            'Pass an iterable value to expect() before calling sequence().',
        ],
        [
            'Calling toHaveCount() on Expectation<string>; matcher requires countable or iterable.',
            29,
            'Pass a countable or iterable value to expect() before calling toHaveCount().',
        ],
    ]);
});

test('every matcher value requirement is enforced without false positives', function (): void {
    $this->analyse([
        __DIR__.'/data/expectation-value-type-exhaustive.php',
    ], [
        [
            'Calling json() on Expectation<int>; matcher requires string.',
            6,
            'Pass a string value to expect() before calling json().',
        ],
        [
            'Calling toStartWith() on Expectation<int>; matcher requires string.',
            10,
            'Pass a string value to expect() before calling toStartWith().',
        ],
        [
            'Calling toEndWith() on Expectation<int>; matcher requires string.',
            14,
            'Pass a string value to expect() before calling toEndWith().',
        ],
        [
            'Calling toBeJson() on Expectation<int>; matcher requires string.',
            18,
            'Pass a string value to expect() before calling toBeJson().',
        ],
        [
            'Calling toBeUppercase() on Expectation<int>; matcher requires string.',
            22,
            'Pass a string value to expect() before calling toBeUppercase().',
        ],
        [
            'Calling toBeLowercase() on Expectation<int>; matcher requires string.',
            26,
            'Pass a string value to expect() before calling toBeLowercase().',
        ],
        [
            'Calling toBeAlphaNumeric() on Expectation<int>; matcher requires string.',
            30,
            'Pass a string value to expect() before calling toBeAlphaNumeric().',
        ],
        [
            'Calling toBeAlpha() on Expectation<int>; matcher requires string.',
            34,
            'Pass a string value to expect() before calling toBeAlpha().',
        ],
        [
            'Calling toBeDigits() on Expectation<int>; matcher requires string.',
            38,
            'Pass a string value to expect() before calling toBeDigits().',
        ],
        [
            'Calling toBeSnakeCase() on Expectation<int>; matcher requires string.',
            42,
            'Pass a string value to expect() before calling toBeSnakeCase().',
        ],
        [
            'Calling toBeKebabCase() on Expectation<int>; matcher requires string.',
            46,
            'Pass a string value to expect() before calling toBeKebabCase().',
        ],
        [
            'Calling toBeCamelCase() on Expectation<int>; matcher requires string.',
            50,
            'Pass a string value to expect() before calling toBeCamelCase().',
        ],
        [
            'Calling toBeStudlyCase() on Expectation<int>; matcher requires string.',
            54,
            'Pass a string value to expect() before calling toBeStudlyCase().',
        ],
        [
            'Calling toBeUuid() on Expectation<int>; matcher requires string.',
            58,
            'Pass a string value to expect() before calling toBeUuid().',
        ],
        [
            'Calling toBeUrl() on Expectation<int>; matcher requires string.',
            62,
            'Pass a string value to expect() before calling toBeUrl().',
        ],
        [
            'Calling toBeSlug() on Expectation<int>; matcher requires string.',
            66,
            'Pass a string value to expect() before calling toBeSlug().',
        ],
        [
            'Calling toMatch() on Expectation<int>; matcher requires string.',
            70,
            'Pass a string value to expect() before calling toMatch().',
        ],
        [
            'Calling toBeDirectory() on Expectation<int>; matcher requires string.',
            74,
            'Pass a string value to expect() before calling toBeDirectory().',
        ],
        [
            'Calling toBeFile() on Expectation<int>; matcher requires string.',
            78,
            'Pass a string value to expect() before calling toBeFile().',
        ],
        [
            'Calling toBeReadableFile() on Expectation<int>; matcher requires string.',
            82,
            'Pass a string value to expect() before calling toBeReadableFile().',
        ],
        [
            'Calling toBeWritableFile() on Expectation<int>; matcher requires string.',
            86,
            'Pass a string value to expect() before calling toBeWritableFile().',
        ],
        [
            'Calling toBeReadableDirectory() on Expectation<int>; matcher requires string.',
            90,
            'Pass a string value to expect() before calling toBeReadableDirectory().',
        ],
        [
            'Calling toBeWritableDirectory() on Expectation<int>; matcher requires string.',
            94,
            'Pass a string value to expect() before calling toBeWritableDirectory().',
        ],
        [
            'Calling toStartWith() on Expectation<array>; matcher requires string.',
            98,
            'Pass a string value to expect() before calling toStartWith().',
        ],
        [
            'Calling toBeUppercase() on Expectation<null>; matcher requires string.',
            102,
            'Pass a string value to expect() before calling toBeUppercase().',
        ],
        [
            'Calling toMatch() on Expectation<true>; matcher requires string.',
            106,
            'Pass a string value to expect() before calling toMatch().',
        ],
        [
            'Calling toBeJson() on Expectation<stdClass>; matcher requires string.',
            110,
            'Pass a string value to expect() before calling toBeJson().',
        ],
        [
            'Calling each() on Expectation<int>; matcher requires iterable.',
            114,
            'Pass an iterable value to expect() before calling each().',
        ],
        [
            'Calling sequence() on Expectation<int>; matcher requires iterable.',
            118,
            'Pass an iterable value to expect() before calling sequence().',
        ],
        [
            'Calling toContainEqual() on Expectation<int>; matcher requires iterable.',
            122,
            'Pass an iterable value to expect() before calling toContainEqual().',
        ],
        [
            'Calling toContainOnlyInstancesOf() on Expectation<string>; matcher requires iterable.',
            126,
            'Pass an iterable value to expect() before calling toContainOnlyInstancesOf().',
        ],
        [
            'Calling toHaveCount() on Expectation<string>; matcher requires countable or iterable.',
            130,
            'Pass a countable or iterable value to expect() before calling toHaveCount().',
        ],
        [
            'Calling toHaveSameSize() on Expectation<int>; matcher requires countable or iterable.',
            134,
            'Pass a countable or iterable value to expect() before calling toHaveSameSize().',
        ],
    ]);
});
