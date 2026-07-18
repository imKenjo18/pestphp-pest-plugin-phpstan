<?php

declare(strict_types=1);

namespace Tests\Rules;

use Pest\PHPStan\Rules\EmptyTestClosureRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$rule = new EmptyTestClosureRule;
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
});

test('empty closures are reported', function (): void {
    $this->analyse([
        __DIR__.'/data/empty-test-closure.php',
    ], [
        [
            "Test 'empty it closure' has an empty closure body. Add assertions or chain ->todo() to mark as pending.",
            5,
        ],
        [
            "Test 'empty test closure' has an empty closure body. Add assertions or chain ->todo() to mark as pending.",
            7,
        ],
    ]);
});

test('empty closures are reported unless the test is marked as todo', function (): void {
    $this->analyse([
        __DIR__.'/data/empty-and-duplicate-exhaustive.php',
    ], [
        [
            "Test 'empty it' has an empty closure body. Add assertions or chain ->todo() to mark as pending.",
            5,
        ],
        [
            "Test 'empty test' has an empty closure body. Add assertions or chain ->todo() to mark as pending.",
            7,
        ],
        [
            "Test 'empty with a chain' has an empty closure body. Add assertions or chain ->todo() to mark as pending.",
            9,
        ],
        [
            "Test 'empty with only a comment' has an empty closure body. Add assertions or chain ->todo() to mark as pending.",
            11,
        ],
    ]);
});
