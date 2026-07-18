<?php

declare(strict_types=1);

use Tests\Type\Fixtures\Post;

it('repeat zero', function (): void {})->repeat(0);

it('repeat negative', function (): void {})->repeat(-1);

it('repeat large negative', function (): void {})->repeat(-100);

it('repeat zero in a longer chain', function (): void {})->group('unit')->repeat(0);

it('empty group', function (): void {})->group('');

it('whitespace group', function (): void {})->group('   ');

it('empty among valid groups', function (): void {})->group('unit', '');

it('no group argument', function (): void {})->group();

it('throws a non throwable', function (): void {})->throws(stdClass::class);

it('throws a non throwable fixture', function (): void {})->throws(Post::class);

it('throws a missing class', function (): void {})->throws(Foo\Bar\MissingException::class);

it('covers a missing class', function (): void {})->coversClass(App\Missing\Klass::class);

it('covers a missing function', function (): void {})->coversFunction('missing_function_name');

it('covers several missing classes', function (): void {})
    ->coversClass(App\Missing\First::class)
    ->coversClass(App\Missing\Second::class);

it('repeat one', function (): void {})->repeat(1);

it('repeat many', function (): void {})->repeat(10);

it('valid group', function (): void {})->group('unit');

it('several valid groups', function (): void {})->group('unit', 'feature');

it('throws a real exception', function (): void {})->throws(RuntimeException::class);

it('throws a real error', function (): void {})->throws(TypeError::class);

it('throws an interface', function (): void {})->throws(Throwable::class);

it('throws a message only', function (): void {})->throws('Something went wrong');

it('throws a message with punctuation', function (): void {})->throws('Not found: id 4');

it('throws a class and a message', function (): void {})->throws(RuntimeException::class, 'boom');

it('covers a real class', function (): void {})->coversClass(Post::class);

it('covers a real function', function (): void {})->coversFunction('strlen');

it('throws no exceptions', function (): void {})->throwsNoExceptions();
