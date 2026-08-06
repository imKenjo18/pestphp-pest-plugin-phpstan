<?php

declare(strict_types=1);

namespace ExpectationChainSubjectNarrowing;

use Tests\Type\Fixtures\Author;
use Tests\Type\Fixtures\Post;

use function PHPStan\Testing\assertType;

/**
 * @return list<mixed>
 */
function items(): array
{
    return [];
}

function testArraySubjectNarrowedLaterInSameChain(): void
{
    $items = items();

    expect($items)->toHaveCount(1)
        ->and($items[0])->toBeInstanceOf(Post::class)
        ->and(assertType(Post::class, $items[0]));
}

function testPlainVariableNarrowedLaterInSameChain(mixed $subject): void
{
    expect($subject)->toBeInstanceOf(Post::class)
        ->and(assertType(Post::class, $subject));
}

function testMultipleDistinctSubjectsEachNarrowedInSameChain(): void
{
    $items = items();

    expect($items)->toHaveCount(2)
        ->and($items[0])->toBeInstanceOf(Post::class)
        ->and($items[1])->toBeInstanceOf(Author::class)
        ->and(assertType(Post::class, $items[0]))
        ->and(assertType(Author::class, $items[1]));
}

function testUsageBeforeALaterInstanceofStepStaysUnnarrowed(): void
{
    $items = items();

    expect(assertType('mixed', $items[0]))
        ->and($items[0])->toBeInstanceOf(Post::class);
}

function testUnrelatedSubjectIsNotNarrowedByAnothersInstanceof(): void
{
    $items = items();

    expect($items[0])->toBeInstanceOf(Post::class)
        ->and(assertType('mixed', $items[1]));
}

function testNarrowingAlsoPersistsPastTheStatement(): void
{
    $items = items();

    expect($items[0])->toBeInstanceOf(Post::class)
        ->and(assertType(Post::class, $items[0]));

    assertType(Post::class, $items[0]);
}
