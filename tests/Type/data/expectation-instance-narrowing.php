<?php

declare(strict_types=1);

namespace ExpectationInstanceNarrowing;

use Tests\Type\Fixtures\Author;
use Tests\Type\Fixtures\Post;

use function PHPStan\Testing\assertType;

function testToBeInstanceOfNarrowsMixedSubject(mixed $subject): void
{
    expect($subject)->toBeInstanceOf(Post::class);

    assertType(Post::class, $subject);
}

function testToBeInstanceOfNarrowsUnionSubject(Post|Author $subject): void
{
    expect($subject)->toBeInstanceOf(Post::class);

    assertType(Post::class, $subject);
}

function testToBeStringDoesNotNarrowSubject(mixed $subject): void
{
    expect($subject)->toBeString();

    assertType('mixed', $subject);
}

function testAndChainedToBeInstanceOfNarrowsPastTheStatement(mixed $subject): void
{
    expect($subject)->toBeString()
        ->and($subject)->toBeInt()
        ->and($subject)->toBeArray()
        ->and($subject)->toBeInstanceOf(Post::class);

    assertType(Post::class, $subject);
}

function testSequentialUnrelatedStatementsStayIndependent(mixed $subject): void
{
    expect($subject)->toBeString();
    expect($subject)->toBeInt();
    expect($subject)->toBeArray();
    expect($subject)->toBeInstanceOf(Post::class);

    assertType(Post::class, $subject);
}

function testChainedMatcherDoesNotNarrowSubject(mixed $subject): void
{
    expect($subject)->toBeObject()->toBeInstanceOf(Post::class);

    assertType('mixed', $subject);
}

function testAndSwitchesSubjectWithoutNarrowingOriginal(mixed $subject, mixed $other): void
{
    expect($subject)->and($other)->toBeInstanceOf(Post::class);

    assertType('mixed', $subject);
    assertType(Post::class, $other);
}

function testChainWithMultipleFollowUpStepsNarrowsPastStatement(mixed $subject): void
{
    expect($subject)->toBeInstanceOf(Post::class)
        ->and($subject->title)->toBe('hello')
        ->and($subject->content)->toBe('world');

    assertType(Post::class, $subject);
}

function testNotDoesNotNarrowSubject(mixed $subject): void
{
    expect($subject)->not()->toBeInstanceOf(Post::class);

    assertType('mixed', $subject);
}

function testExpressionInsideConditionIsNotNarrowed(mixed $subject): void
{
    if (expect($subject)->toBeInstanceOf(Post::class)) {
        assertType('mixed', $subject);
    }
}
