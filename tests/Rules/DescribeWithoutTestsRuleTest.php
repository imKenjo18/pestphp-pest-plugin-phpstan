<?php

declare(strict_types=1);

namespace Tests\Rules;

use Pest\PHPStan\Rules\DescribeWithoutTestsRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$rule = new DescribeWithoutTestsRule;
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
});

test('describe without tests is reported', function (): void {
    $this->analyse([
        __DIR__.'/data/describe-without-tests.php',
    ], [
        [
            "describe() block 'empty group' contains no tests.",
            5,
        ],
        [
            "describe() block 'hooks only' contains no tests.",
            8,
        ],
        [
            "describe() block 'hooks with chain only' contains no tests.",
            14,
        ],
    ]);
});

test('describe blocks without tests are reported across nested shapes', function (): void {
    $this->analyse([
        __DIR__.'/data/describe-structure-exhaustive.php',
    ], [
        [
            "describe() block 'completely empty' contains no tests.",
            5,
        ],
        [
            "describe() block 'hooks only' contains no tests.",
            7,
        ],
        [
            "describe() block 'statements but no tests' contains no tests.",
            12,
        ],
        [
            "describe() block 'inner empty' contains no tests.",
            18,
        ],
    ]);
});
