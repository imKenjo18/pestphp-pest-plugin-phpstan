<?php

declare(strict_types=1);

use Pest\PHPStan\Analysis\Expectation\ExpectationMatcherRegistry;
use Pest\PHPStan\Analysis\Expectation\MatcherRequirementRegistry;
use Pest\PHPStan\Diagnostics\PestDiagnosticIdentifiers;
use Pest\PHPStan\Diagnostics\PestDiagnostics;

test('invalid matcher diagnostics carry identifier, message, and tip', function (): void {
    $diagnostic = PestDiagnostics::invalidMatcherType('toBeAlpha', 'int', MatcherRequirementRegistry::STRING);

    expect($diagnostic->identifier)->toBe(PestDiagnosticIdentifiers::EXPECTATION_REQUIRES_STRING)
        ->and($diagnostic->message)->toBe('Calling toBeAlpha() on Expectation<int>; matcher requires string.')
        ->and($diagnostic->tip)->toBe('Pass a string value to expect() before calling toBeAlpha().');
});

test('countable requirements produce a readable label', function (): void {
    $diagnostic = PestDiagnostics::invalidMatcherType('toHaveCount', 'int', MatcherRequirementRegistry::COUNTABLE_OR_ITERABLE);

    expect($diagnostic->identifier)->toBe(PestDiagnosticIdentifiers::EXPECTATION_REQUIRES_COUNTABLE_OR_ITERABLE)
        ->and($diagnostic->message)->toBe('Calling toHaveCount() on Expectation<int>; matcher requires countable or iterable.')
        ->and($diagnostic->tip)->toBe('Pass a countable or iterable value to expect() before calling toHaveCount().');
});

test('impossible and redundant diagnostics expose their identifiers', function (): void {
    $impossible = PestDiagnostics::impossibleExpectation('toBeString', 'int');
    $redundant = PestDiagnostics::redundantExpectation('toBeString', 'string');

    expect($impossible->identifier)->toBe(PestDiagnosticIdentifiers::EXPECTATION_IMPOSSIBLE)
        ->and($impossible->message)->toBe('Calling toBeString() on Expectation<int>; assertion is impossible.')
        ->and($redundant->identifier)->toBe(PestDiagnosticIdentifiers::EXPECTATION_REDUNDANT)
        ->and($redundant->message)->toBe('Calling toBeString() on Expectation<string>; assertion is redundant.');
});

test('lifecycle diagnostics carry the line and replacement hook', function (): void {
    $diagnostic = PestDiagnostics::invalidLifecycleThisUsage(
        'beforeAll',
        'beforeEach',
        PestDiagnosticIdentifiers::LIFECYCLE_BEFORE_ALL_THIS_USAGE,
        12,
    );

    expect($diagnostic->identifier)->toBe(PestDiagnosticIdentifiers::LIFECYCLE_BEFORE_ALL_THIS_USAGE)
        ->and($diagnostic->message)->toBe('beforeAll() runs in static context — $this is not available. Use beforeEach() instead.')
        ->and($diagnostic->line)->toBe(12);
});

test('diagnostic identifiers are unique and canonically namespaced', function (): void {
    $identifiers = (new ReflectionClass(PestDiagnosticIdentifiers::class))->getConstants();

    expect($identifiers)->not->toBeEmpty()
        ->and(array_values($identifiers))->toBe(array_values(array_unique($identifiers)));

    foreach ($identifiers as $name => $value) {
        expect($value)->toBeString()
            ->and($value)->toStartWith('pest.')
            ->and($value)->toMatch('/^pest(\.[a-z][a-zA-Z]*)+$/', sprintf('Identifier %s is not canonically formatted.', $name));
    }
});

test('matcher metadata is cached per matcher name', function (): void {
    $registry = new ExpectationMatcherRegistry;

    $first = $registry->metadataFor('toBeString');
    $second = $registry->metadataFor('toBeString');

    expect($first)->not->toBeNull()
        ->and($second)->toBe($first)
        ->and($first?->assertion)->toBe('string')
        ->and($registry->metadataFor('toHaveCount')?->requirement)->toBe(MatcherRequirementRegistry::COUNTABLE_OR_ITERABLE)
        ->and($registry->metadataFor('unknownMatcher'))->toBeNull();
});
