<?php

declare(strict_types=1);

namespace TestHookPropertiesExhaustive;

use Tests\Type\Fixtures\Author;
use Tests\Type\Fixtures\Post;

use function PHPStan\Testing\assertType;

function resolveNullablePost(): ?Post
{
    return random_int(0, 1) === 1 ? new Post : null;
}

function testAfterEachAssignment(): void
{
    afterEach(function (): void {
        $this->post = new Post;
    });

    it('resolves a property assigned in afterEach', function (): void {
        assertType(Post::class, $this->post);
    });
}

function testPropertyVisibleInsideAnotherHook(): void
{
    beforeEach(function (): void {
        $this->post = new Post;
    });

    afterEach(function (): void {
        assertType(Post::class, $this->post);
    });
}

function testNullableAssignment(): void
{
    beforeEach(function (): void {
        $this->maybePost = resolveNullablePost();
    });

    it('falls back to mixed for a plain function call', function (): void {
        assertType('mixed', $this->maybePost);
    });
}

function testShapedArrayAssignment(): void
{
    beforeEach(function (): void {
        $this->config = ['id' => 1, 'name' => 'pest'];
    });

    it('resolves a shaped array property', function (): void {
        assertType("array{id: 1, name: 'pest'}", $this->config);
    });
}

function testListAssignment(): void
{
    beforeEach(function (): void {
        $this->items = [1, 2, 3];
    });

    it('resolves a list property', function (): void {
        assertType('array{1, 2, 3}', $this->items);
    });
}

function testVarAnnotationUnion(): void
{
    beforeEach(function (): void {
        /** @var Author|Post $subject */
        $subject = new Post;
        $this->subject = $subject;
    });

    // A compound @var must never be truncated to its first class name; the reader
    it('does not truncate a union @var annotation', function (): void {
        assertType(Post::class, $this->subject);
    });
}

function testArrayShorthandVarAnnotation(): void
{
    beforeEach(function (): void {
        /** @var Post[] $posts */
        $posts = [new Post];
        $this->posts = $posts;
    });

    it('does not collapse an array shorthand @var to its element type', function (): void {
        assertType('array{Tests\Type\Fixtures\Post}', $this->posts);
    });
}

function testGenericVarAnnotation(): void
{
    beforeEach(function (): void {
        /** @var array<int, Post> $indexed */
        $indexed = [new Post];
        $this->indexed = $indexed;
    });

    it('does not collapse a generic @var to its container name', function (): void {
        assertType('array{Tests\Type\Fixtures\Post}', $this->indexed);
    });
}

function testNullableVarAnnotation(): void
{
    beforeEach(function (): void {
        /** @var ?Post $nullable */
        $nullable = new Post;
        $this->nullable = $nullable;
    });

    it('falls back to the real assigned type for a nullable @var', function (): void {
        assertType(Post::class, $this->nullable);
    });
}

function testStaticFactoryAssignment(): void
{
    beforeEach(function (): void {
        $this->post = Post::make();
    });

    it('resolves a property from a static factory', function (): void {
        assertType(Post::class, $this->post);
    });
}

function testChainedPropertyAssignment(): void
{
    beforeEach(function (): void {
        $post = new Post;
        $this->author = $post->author;
    });

    it('falls back to mixed for a property read from another object', function (): void {
        assertType('mixed', $this->author);
    });
}

function testThreeHooksUnionTheProperty(): void
{
    beforeEach(function (): void {
        $this->value = 1;
    });

    beforeEach(function (): void {
        $this->value = 'two';
    });

    beforeEach(function (): void {
        $this->value = 3.0;
    });

    it('unions every assigned type', function (): void {
        assertType("1|3.0|'two'", $this->value);
    });
}

function testNestedDescribeHooks(): void
{
    describe('outer', function (): void {
        beforeEach(function (): void {
            $this->outer = new Post;
        });

        describe('inner', function (): void {
            beforeEach(function (): void {
                $this->inner = new Author;
            });

            it('sees both hook properties', function (): void {
                assertType(Post::class, $this->outer);
                assertType(Author::class, $this->inner);
            });
        });
    });
}

function testPropertyUsedInsideExpectation(): void
{
    beforeEach(function (): void {
        $this->post = new Post;
    });

    it('flows the hook property type into expect()', function (): void {
        assertType('Pest\Expectation<Tests\Type\Fixtures\Post>', expect($this->post));
        assertType('string', $this->post->title);
    });
}

function testUnknownPropertyRemainsMixed(): void
{
    beforeEach(function (): void {
        $this->known = new Post;
    });

    it('leaves unrelated properties as mixed', function (): void {
        assertType('mixed', $this->unknown);
    });
}
