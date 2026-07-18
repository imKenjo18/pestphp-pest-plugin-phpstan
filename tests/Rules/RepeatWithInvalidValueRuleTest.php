<?php

declare(strict_types=1);

namespace Tests\Rules;

use Pest\PHPStan\Rules\RepeatWithInvalidValueRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$rule = new RepeatWithInvalidValueRule;
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
});

test('repeat with invalid values is reported', function (): void {
    $this->analyse([
        __DIR__.'/data/repeat-invalid-value.php',
    ], [
        [
            'repeat() requires a value greater than 0, got 0.',
            5,
        ],
        [
            'repeat() requires a value greater than 0, got -1.',
            8,
        ],
    ]);
});

test('invalid repeat values are reported without false positives', function (): void {
    $this->analyse([
        __DIR__.'/data/test-call-validation-exhaustive.php',
    ], [
        [
            'repeat() requires a value greater than 0, got 0.',
            7,
        ],
        [
            'repeat() requires a value greater than 0, got -1.',
            9,
        ],
        [
            'repeat() requires a value greater than 0, got -100.',
            11,
        ],
        [
            'repeat() requires a value greater than 0, got 0.',
            13,
        ],
    ]);
});
