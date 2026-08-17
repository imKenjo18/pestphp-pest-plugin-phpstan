<?php

declare(strict_types=1);

namespace ExpectationNarrowing;

use RuntimeException;

use function PHPStan\Testing\assertType;

function testToBeIntNarrows(): void
{
    /** @var int|string $value */
    $value = random_int(0, 1) === 1 ? 1 : 'a';
    expect($value)->toBeInt();
    assertType('int', $value);
}

function testToBeStringNarrows(): void
{
    /** @var int|string $value */
    $value = random_int(0, 1) === 1 ? 1 : 'a';
    expect($value)->toBeString();
    assertType('string', $value);
}

function testToBeNullNarrows(): void
{
    /** @var string|null $value */
    $value = random_int(0, 1) === 1 ? 'a' : null;
    expect($value)->toBeNull();
    assertType('null', $value);
}

function testToBeInstanceOfNarrows(): void
{
    $value = random_int(0, 1) === 1 ? new RuntimeException('x') : 'a';
    expect($value)->toBeInstanceOf(RuntimeException::class);
    assertType('RuntimeException', $value);
}

function testToBeTrueNarrows(): void
{
    $value = random_int(0, 1) === 1;
    expect($value)->toBeTrue();
    assertType('true', $value);
}

function testChainedMatchersAllNarrow(): void
{
    /** @var string|null $value */
    $value = random_int(0, 1) === 1 ? 'a' : null;
    expect($value)->toBeString()->toStartWith('a');
    assertType('string', $value);
}

function testAndRebindsTheSubject(): void
{
    /** @var int|string $first */
    $first = random_int(0, 1) === 1 ? 1 : 'a';
    /** @var int|string $second */
    $second = random_int(0, 1) === 1 ? 1 : 'a';
    expect($first)->toBeInt()->and($second)->toBeString();
    assertType('int', $first);
    assertType('string', $second);
}

function testWhenIsPassthrough(): void
{
    /** @var int|string $value */
    $value = random_int(0, 1) === 1 ? 1 : 'a';
    expect($value)->when(true, fn ($e) => $e)->toBeInt();
    assertType('int', $value);
}

function testJsonBreaksTheSubjectLink(): void
{
    /** @var int|string $value */
    $value = random_int(0, 1) === 1 ? 1 : 'a';
    expect($value)->toBeString()->json()->toBeArray();
    assertType('string', $value);
}

function testEachDoesNotNarrowTheSubject(): void
{
    /** @var array<int|string> $values */
    $values = [];
    expect($values)->each->toBeInt();
    assertType('array<int|string>', $values);
}

function testHigherOrderPropertyDoesNotNarrow(): void
{
    /** @var int|string $value */
    $value = random_int(0, 1) === 1 ? 1 : 'a';
    expect($value)->toBeInt()->foo->toBeString();
    assertType('int|string', $value);
}

function testExpectWithoutArgumentsIsIgnored(): void
{
    expect()->toBeNull();
}

function testAssignedExpectationAlsoNarrowsTheSubject(): void
{
    /** @var int|string $value */
    $value = random_int(0, 1) === 1 ? 1 : 'a';
    $expectation = expect($value)->toBeInt();
    assertType('Pest\Expectation<int>', $expectation);
    assertType('int', $value);
}
