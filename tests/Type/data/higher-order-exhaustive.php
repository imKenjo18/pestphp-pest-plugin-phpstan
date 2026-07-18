<?php

declare(strict_types=1);

namespace HigherOrderExhaustive;

use Tests\Type\Fixtures\Author;
use Tests\Type\Fixtures\Post;

use function PHPStan\Testing\assertType;

function testSingleProperty(): void
{
    $post = new Post;
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Post>, string>', expect($post)->title);
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Post>, string>', expect($post)->content);
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Post>, Tests\Type\Fixtures\Author>', expect($post)->author);
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Post>, Tests\Type\Fixtures\Author|null>', expect($post)->editor);
}

function testNestedProperty(): void
{
    $post = new Post;
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Post>, string>', expect($post)->author->name);
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Post>, string>', expect($post)->author->email);
}

function testPropertyThenAssertionResetsToValue(): void
{
    $post = new Post;
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Post>, string>', expect($post)->title->toBe('x')->content);
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Post>, Tests\Type\Fixtures\Author>', expect($post)->title->toBe('x')->author);
}

function testMultipleAssertionsInChain(): void
{
    $post = new Post;
    $result = expect($post)
        ->title->toBe('a')->toBeString()
        ->content->toContain('b');
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Post>, Tests\Type\Fixtures\Post>', $result);
}

function testNullableSubject(): void
{
    /** @var Post|null $post */
    $post = null;
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Post|null>, string>', expect($post)->title);
    assertType('Pest\Expectations\OppositeExpectation<Tests\Type\Fixtures\Post|null>', expect($post)->not);
}

function testAndIntoHigherOrder(): void
{
    $post = new Post;
    $result = expect(true)->toBeTrue()
        ->and($post)
        ->title;
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Post>, string>', $result);
}

function testAuthorDirectExpectation(): void
{
    $author = new Author;
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Author>, string>', expect($author)->name);
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Author>, string>', expect($author)->email);
}

function testMethodCallHigherOrder(): void
{
    $post = new Post;

    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Post>, string>', expect($post)->author->name);

    $result = expect($post)->author->name->toBe('Nuno');
    assertType('Pest\Expectations\HigherOrderExpectation<Pest\Expectation<Tests\Type\Fixtures\Post>, Tests\Type\Fixtures\Post>', $result);
}
