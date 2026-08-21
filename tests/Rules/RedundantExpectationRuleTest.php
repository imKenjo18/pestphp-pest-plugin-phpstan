<?php

declare(strict_types=1);

namespace Tests\Rules;

use Pest\PHPStan\Rules\RedundantExpectationRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
    RuleTestCase::$rule = RuleTestCase::resolveRule(RedundantExpectationRule::class);
});

test('redundant expectations are reported', function (): void {
    $this->analyse([
        __DIR__.'/data/redundant-expectation.php',
    ], [
        [
            'Calling toBeTrue() on Expectation<true>; assertion is redundant.',
            6,
            'The expectation value is already guaranteed to satisfy toBeTrue().',
        ],
        [
            'Calling toBeFalse() on Expectation<false>; assertion is redundant.',
            10,
            'The expectation value is already guaranteed to satisfy toBeFalse().',
        ],
        [
            'Calling toBeBool() on Expectation<true>; assertion is redundant.',
            14,
            'The expectation value is already guaranteed to satisfy toBeBool().',
        ],
        [
            'Calling toBeString() on Expectation<string>; assertion is redundant.',
            18,
            'The expectation value is already guaranteed to satisfy toBeString().',
        ],
        [
            'Calling toBeInt() on Expectation<int>; assertion is redundant.',
            22,
            'The expectation value is already guaranteed to satisfy toBeInt().',
        ],
        [
            'Calling toBeFloat() on Expectation<float>; assertion is redundant.',
            26,
            'The expectation value is already guaranteed to satisfy toBeFloat().',
        ],
        [
            'Calling toBeNull() on Expectation<null>; assertion is redundant.',
            30,
            'The expectation value is already guaranteed to satisfy toBeNull().',
        ],
        [
            'Calling toBeArray() on Expectation<array>; assertion is redundant.',
            34,
            'The expectation value is already guaranteed to satisfy toBeArray().',
        ],
        [
            'Calling toBeScalar() on Expectation<string>; assertion is redundant.',
            38,
            'The expectation value is already guaranteed to satisfy toBeScalar().',
        ],
        [
            'Calling toBeNumeric() on Expectation<int>; assertion is redundant.',
            42,
            'The expectation value is already guaranteed to satisfy toBeNumeric().',
        ],
        [
            'Calling toBeInstanceOf() on Expectation<stdClass>; assertion is redundant.',
            46,
            'The expectation value is already guaranteed to satisfy toBeInstanceOf().',
        ],
    ]);
});

test('redundant chains ignore earlier invalid matcher steps', function (): void {
    $this->analyse([
        __DIR__.'/data/redundant-expectation-chain.php',
    ], [
        [
            'Calling toBeString() on Expectation<string>; assertion is redundant.',
            10,
            'The expectation value is already guaranteed to satisfy toBeString().',
        ],
    ]);
});

test('inheritance and scalar redundancies are reported', function (): void {
    $this->analyse([
        __DIR__.'/data/redundant-expectation-instanceof.php',
    ], [
        [
            'Calling toBeInstanceOf() on Expectation<RuntimeException>; assertion is redundant.',
            6,
            'The expectation value is already guaranteed to satisfy toBeInstanceOf().',
        ],
        [
            'Calling toBeInstanceOf() on Expectation<RuntimeException>; assertion is redundant.',
            10,
            'The expectation value is already guaranteed to satisfy toBeInstanceOf().',
        ],
        [
            'Calling toBeScalar() on Expectation<int|string>; assertion is redundant.',
            17,
            'The expectation value is already guaranteed to satisfy toBeScalar().',
        ],
        [
            'Calling toBeNumeric() on Expectation<string>; assertion is redundant.',
            24,
            'The expectation value is already guaranteed to satisfy toBeNumeric().',
        ],
    ]);
});

test('redundant semantic chains avoid duplicate diagnostics', function (): void {
    $this->analyse([
        __DIR__.'/data/redundant-expectation-semantic-chain.php',
    ], [
        [
            'Calling toBeString() on Expectation<string>; assertion is redundant.',
            6,
            'The expectation value is already guaranteed to satisfy toBeString().',
        ],
    ]);
});

test('redundancy is reported across separate expect() statements', function (): void {
    $this->analyse([
        __DIR__.'/data/redundant-expectation-cross-statement.php',
    ], [
        [
            'Calling toBeInt() on Expectation<int>; assertion is redundant.',
            8,
            'The expectation value is already guaranteed to satisfy toBeInt().',
        ],
    ]);
});

test('every redundant matcher combination is reported without false positives', function (): void {
    $this->analyse([
        __DIR__.'/data/redundant-expectation-exhaustive.php',
    ], [
        [
            'Calling toBeString() on Expectation<string>; assertion is redundant.',
            6,
            'The expectation value is already guaranteed to satisfy toBeString().',
        ],
        [
            'Calling toBeInt() on Expectation<int>; assertion is redundant.',
            10,
            'The expectation value is already guaranteed to satisfy toBeInt().',
        ],
        [
            'Calling toBeFloat() on Expectation<float>; assertion is redundant.',
            14,
            'The expectation value is already guaranteed to satisfy toBeFloat().',
        ],
        [
            'Calling toBeTrue() on Expectation<true>; assertion is redundant.',
            18,
            'The expectation value is already guaranteed to satisfy toBeTrue().',
        ],
        [
            'Calling toBeFalse() on Expectation<false>; assertion is redundant.',
            22,
            'The expectation value is already guaranteed to satisfy toBeFalse().',
        ],
        [
            'Calling toBeBool() on Expectation<true>; assertion is redundant.',
            26,
            'The expectation value is already guaranteed to satisfy toBeBool().',
        ],
        [
            'Calling toBeBool() on Expectation<false>; assertion is redundant.',
            30,
            'The expectation value is already guaranteed to satisfy toBeBool().',
        ],
        [
            'Calling toBeNull() on Expectation<null>; assertion is redundant.',
            34,
            'The expectation value is already guaranteed to satisfy toBeNull().',
        ],
        [
            'Calling toBeArray() on Expectation<array>; assertion is redundant.',
            38,
            'The expectation value is already guaranteed to satisfy toBeArray().',
        ],
        [
            'Calling toBeIterable() on Expectation<array<int, int>>; assertion is redundant.',
            42,
            'The expectation value is already guaranteed to satisfy toBeIterable().',
        ],
        [
            'Calling toBeNumeric() on Expectation<int>; assertion is redundant.',
            46,
            'The expectation value is already guaranteed to satisfy toBeNumeric().',
        ],
        [
            'Calling toBeNumeric() on Expectation<float>; assertion is redundant.',
            50,
            'The expectation value is already guaranteed to satisfy toBeNumeric().',
        ],
        [
            'Calling toBeScalar() on Expectation<int>; assertion is redundant.',
            54,
            'The expectation value is already guaranteed to satisfy toBeScalar().',
        ],
        [
            'Calling toBeScalar() on Expectation<string>; assertion is redundant.',
            58,
            'The expectation value is already guaranteed to satisfy toBeScalar().',
        ],
        [
            'Calling toBeScalar() on Expectation<true>; assertion is redundant.',
            62,
            'The expectation value is already guaranteed to satisfy toBeScalar().',
        ],
        [
            'Calling toBeObject() on Expectation<stdClass>; assertion is redundant.',
            66,
            'The expectation value is already guaranteed to satisfy toBeObject().',
        ],
        [
            'Calling toBeInstanceOf() on Expectation<stdClass>; assertion is redundant.',
            70,
            'The expectation value is already guaranteed to satisfy toBeInstanceOf().',
        ],
        [
            'Calling toBeInstanceOf() on Expectation<RuntimeException>; assertion is redundant.',
            74,
            'The expectation value is already guaranteed to satisfy toBeInstanceOf().',
        ],
        [
            'Calling toBeString() on Expectation<string>; assertion is redundant.',
            80,
            'The expectation value is already guaranteed to satisfy toBeString().',
        ],
        [
            'Calling toBeArray() on Expectation<array<int>>; assertion is redundant.',
            86,
            'The expectation value is already guaranteed to satisfy toBeArray().',
        ],
        [
            'Calling toBeNumeric() on Expectation<string>; assertion is redundant.',
            92,
            'The expectation value is already guaranteed to satisfy toBeNumeric().',
        ],
        [
            'Calling toBeScalar() on Expectation<int|string>; assertion is redundant.',
            98,
            'The expectation value is already guaranteed to satisfy toBeScalar().',
        ],
    ]);
});
