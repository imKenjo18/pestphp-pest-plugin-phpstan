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
