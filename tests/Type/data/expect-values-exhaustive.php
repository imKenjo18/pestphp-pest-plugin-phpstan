<?php

declare(strict_types=1);

namespace ExpectValuesExhaustive;

use ArrayIterator;
use ArrayObject;
use Generator;
use SplStack;
use stdClass;

use function PHPStan\Testing\assertType;

function testScalarLiterals(): void
{
    assertType("Pest\Expectation<'hello'>", expect('hello'));
    assertType("Pest\Expectation<''>", expect(''));
    assertType('Pest\Expectation<0>', expect(0));
    assertType('Pest\Expectation<42>', expect(42));
    assertType('Pest\Expectation<-7>', expect(-7));
    assertType('Pest\Expectation<3.14>', expect(3.14));
    assertType('Pest\Expectation<0.0>', expect(0.0));
    assertType('Pest\Expectation<true>', expect(true));
    assertType('Pest\Expectation<false>', expect(false));
    assertType('Pest\Expectation<null>', expect(null));
    assertType('Pest\Expectation<null>', expect());
}

function testArrayLiterals(): void
{
    assertType('Pest\Expectation<array{}>', expect([]));
    assertType('Pest\Expectation<array{1, 2, 3}>', expect([1, 2, 3]));
    assertType("Pest\Expectation<array{a: 1, b: 2}>", expect(['a' => 1, 'b' => 2]));
    assertType("Pest\Expectation<array{id: 1, name: 'pest'}>", expect(['id' => 1, 'name' => 'pest']));
    assertType('Pest\Expectation<array{array{1}, array{2}}>', expect([[1], [2]]));
}

function testObjectLiterals(): void
{
    assertType('Pest\Expectation<stdClass>', expect(new stdClass));
    assertType('Pest\Expectation<ArrayObject<int, int>>', expect(new ArrayObject([1, 2])));
    assertType('Pest\Expectation<ArrayIterator<int, int>>', expect(new ArrayIterator([1, 2])));
    assertType('Pest\Expectation<SplStack<mixed>>', expect(new SplStack));
}

function testTypedVariables(): void
{
    /** @var string $string */
    $string = 'value';
    assertType('Pest\Expectation<string>', expect($string));

    /** @var int $int */
    $int = 1;
    assertType('Pest\Expectation<int>', expect($int));

    /** @var float $float */
    $float = 1.0;
    assertType('Pest\Expectation<float>', expect($float));

    /** @var bool $bool */
    $bool = true;
    assertType('Pest\Expectation<bool>', expect($bool));

    /** @var array<int, string> $list */
    $list = ['a'];
    assertType('Pest\Expectation<array<int, string>>', expect($list));

    /** @var non-empty-string $nonEmpty */
    $nonEmpty = 'x';
    assertType('Pest\Expectation<non-empty-string>', expect($nonEmpty));

    /** @var positive-int $positive */
    $positive = 3;
    assertType('Pest\Expectation<int<1, max>>', expect($positive));
}

function testUnionVariables(): void
{
    /** @var int|string $intOrString */
    $intOrString = 1;
    assertType('Pest\Expectation<int|string>', expect($intOrString));

    /** @var string|null $nullable */
    $nullable = null;
    assertType('Pest\Expectation<string|null>', expect($nullable));

    /** @var int|float|bool $scalarUnion */
    $scalarUnion = 1;
    assertType('Pest\Expectation<bool|float|int>', expect($scalarUnion));
}

function testGeneratorAndIterables(): void
{
    /** @var Generator<int, string, mixed, void> $generator */
    $generator = (function (): Generator {
        yield 'a';
    })();
    assertType('Pest\Expectation<Generator<int, string, mixed, void>>', expect($generator));

    /** @var iterable<int, int> $iterable */
    $iterable = [1, 2];
    assertType('Pest\Expectation<iterable<int, int>>', expect($iterable));
}

function testValueProperty(): void
{
    /** @var int|string $value */
    $value = 1;
    assertType('int|string', expect($value)->value);

    assertType('42', expect(42)->value);
    assertType("'hello'", expect('hello')->value);
    assertType('array{}', expect([])->value);
}
