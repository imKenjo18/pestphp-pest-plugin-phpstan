<?php

declare(strict_types=1);

namespace Tests\Rules;

use Pest\PHPStan\Rules\DisallowedCallInDescribeRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$rule = new DisallowedCallInDescribeRule;
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
});

test('before all in describe is reported', function (): void {
    $this->analyse([
        __DIR__.'/data/disallowed-call-in-describe.php',
    ], [
        [
            'beforeAll() cannot be used inside describe() blocks. Use beforeEach() instead.',
            6,
        ],
        [
            'afterAll() cannot be used inside describe() blocks. Use afterEach() instead.',
            15,
        ],
        [
            'beforeAll() cannot be used inside describe() blocks. Use beforeEach() instead.',
            24,
        ],
        [
            'afterAll() cannot be used inside describe() blocks. Use afterEach() instead.',
            26,
        ],
        [
            'beforeAll() cannot be used inside describe() blocks. Use beforeEach() instead.',
            58,
        ],
        [
            'beforeAll() cannot be used inside describe() blocks. Use beforeEach() instead.',
            69,
        ],
    ]);
});

test('disallowed hooks are reported across nested describe shapes', function (): void {
    $this->analyse([
        __DIR__.'/data/describe-structure-exhaustive.php',
    ], [
        [
            'beforeAll() cannot be used inside describe() blocks. Use beforeEach() instead.',
            22,
        ],
        [
            'afterAll() cannot be used inside describe() blocks. Use afterEach() instead.',
            30,
        ],
        [
            'beforeAll() cannot be used inside describe() blocks. Use beforeEach() instead.',
            38,
        ],
        [
            'afterAll() cannot be used inside describe() blocks. Use afterEach() instead.',
            39,
        ],
        [
            'beforeAll() cannot be used inside describe() blocks. Use beforeEach() instead.',
            48,
        ],
        [
            'beforeAll() cannot be used inside describe() blocks. Use beforeEach() instead.',
            58,
        ],
    ]);
});
