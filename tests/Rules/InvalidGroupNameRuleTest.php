<?php

declare(strict_types=1);

namespace Tests\Rules;

use Pest\PHPStan\Rules\InvalidGroupNameRule;
use Tests\RuleTestCase;

beforeAll(function (): void {
    RuleTestCase::$rule = new InvalidGroupNameRule;
    RuleTestCase::$additionalConfigFiles = [
        __DIR__.'/../extension.neon',
    ];
});

test('empty group name is reported', function (): void {
    $this->analyse([
        __DIR__.'/data/invalid-group-name.php',
    ], [
        [
            'group() requires a non-empty string argument.',
            5,
        ],
        [
            'group() requires a non-empty string argument.',
            9,
        ],
    ]);
});

test('config-style group names are validated', function (): void {
    $this->analyse([
        __DIR__.'/data/invalid-group-name-config.php',
    ], [
        [
            'group() requires a non-empty string argument.',
            7,
        ],
        [
            'group() requires a non-empty string argument.',
            8,
        ],
        [
            'group() requires at least one non-empty string argument.',
            9,
        ],
        [
            'group() requires a non-empty string argument.',
            10,
        ],
        [
            'group() requires a non-empty string argument.',
            11,
        ],
        [
            'group() requires at least one non-empty string argument.',
            12,
        ],
    ]);
});
