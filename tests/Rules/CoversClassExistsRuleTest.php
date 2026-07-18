<?php

declare(strict_types=1);

namespace Tests\Rules;

use Pest\PHPStan\Rules\CoversClassExistsRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
    RuleTestCase::$rule = RuleTestCase::resolveRule(CoversClassExistsRule::class);
});

test('non existent class in covers class is reported', function (): void {
    $this->analyse([
        __DIR__.'/data/covers-class-exists.php',
    ], [
        [
            'Class App\NonExistent\FakeClass referenced in coversClass() does not exist.',
            7,
        ],
        [
            'Class App\NonExistent\SecondFakeClass referenced in coversClass() does not exist.',
            14,
        ],
        [
            'Function missing_test_helper() referenced in coversFunction() does not exist.',
            17,
        ],
    ]);
});

test('missing covers references are reported without false positives', function (): void {
    $this->analyse([
        __DIR__.'/data/test-call-validation-exhaustive.php',
    ], [
        [
            'Class App\\Missing\\Klass referenced in coversClass() does not exist.',
            29,
        ],
        [
            'Function missing_function_name() referenced in coversFunction() does not exist.',
            31,
        ],
        [
            'Class App\\Missing\\First referenced in coversClass() does not exist.',
            33,
        ],
        [
            'Class App\\Missing\\Second referenced in coversClass() does not exist.',
            33,
        ],
    ]);
});
