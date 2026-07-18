<?php

declare(strict_types=1);

namespace ExpectationMatchersExhaustive;

use ArrayIterator;
use Countable;
use RuntimeException;
use stdClass;
use Stringable;
use Throwable;
use Traversable;

use function PHPStan\Testing\assertType;

function getIntOrString(): int|string
{
    return random_int(0, 1) === 1 ? 1 : 'a';
}

function getStringOrArray(): string|array
{
    return random_int(0, 1) === 1 ? 'a' : [];
}

function testNarrowingMatchersOnUnions(): void
{
    assertType('Pest\Expectation<int>', expect(getIntOrString())->toBeInt());
    assertType('Pest\Expectation<string>', expect(getIntOrString())->toBeString());
    assertType('Pest\Expectation<array<int|string, mixed>>', expect(getStringOrArray())->toBeArray());
    assertType('Pest\Expectation<string>', expect(getStringOrArray())->toBeString());
}

function testNarrowingReflectsOnValue(): void
{
    assertType('int', expect(getIntOrString())->toBeInt()->value);
    assertType('string', expect(getIntOrString())->toBeString()->value);
}

function testFloatNarrowing(): void
{
    /** @var int|float $value */
    $value = 1.0;
    assertType('Pest\Expectation<float>', expect($value)->toBeFloat());
    assertType('float', expect($value)->toBeFloat()->value);
}

function testBoolNarrowing(): void
{
    /** @var bool|string $value */
    $value = true;
    assertType('Pest\Expectation<bool>', expect($value)->toBeBool());

    /** @var bool $flag */
    $flag = true;
    assertType('Pest\Expectation<true>', expect($flag)->toBeTrue());
    assertType('Pest\Expectation<false>', expect($flag)->toBeFalse());
}

function testNullNarrowing(): void
{
    /** @var string|null $value */
    $value = null;
    assertType('Pest\Expectation<null>', expect($value)->toBeNull());
    assertType('null', expect($value)->toBeNull()->value);
}

function testObjectNarrowing(): void
{
    /** @var object|string $value */
    $value = new stdClass;
    assertType('Pest\Expectation<object>', expect($value)->toBeObject());
}

function testCallableNarrowing(): void
{
    /** @var callable|string $value */
    $value = 'strlen';
    assertType('Pest\Expectation<callable(): mixed>', expect($value)->toBeCallable());
}

function testIterableNarrowing(): void
{
    /** @var ArrayIterator<int, int>|string $value */
    $value = new ArrayIterator([1]);
    assertType('Pest\Expectation<ArrayIterator<int, int>>', expect($value)->toBeIterable());
}

function testListNarrowing(): void
{
    /** @var array<int>|string $value */
    $value = [1, 2];
    assertType('Pest\Expectation<list<int>>', expect($value)->toBeList());
}

function testNumericNarrowing(): void
{
    /** @var numeric-string|bool $value */
    $value = '1';
    assertType('Pest\Expectation<numeric-string>', expect($value)->toBeNumeric());

    /** @var int|array<int> $intOrArray */
    $intOrArray = 1;
    assertType('Pest\Expectation<int>', expect($intOrArray)->toBeNumeric());
}

function testScalarNarrowing(): void
{
    /** @var int|string|array<int> $value */
    $value = 1;
    assertType('Pest\Expectation<int|string>', expect($value)->toBeScalar());
}

function testInstanceOfNarrowing(): void
{
    /** @var stdClass|string $value */
    $value = new stdClass;
    assertType('Pest\Expectation<stdClass>', expect($value)->toBeInstanceOf(stdClass::class));
    assertType('stdClass', expect($value)->toBeInstanceOf(stdClass::class)->value);

    /** @var Throwable|int $throwable */
    $throwable = new RuntimeException;
    assertType('Pest\Expectation<Throwable>', expect($throwable)->toBeInstanceOf(Throwable::class));
}

function testAssertionMatchersPreserveType(): void
{
    /** @var string $string */
    $string = 'hello';
    assertType('Pest\Expectation<string>', expect($string)->toBe('hello'));
    assertType('Pest\Expectation<string>', expect($string)->toEqual('hello'));
    assertType('Pest\Expectation<string>', expect($string)->toBeTruthy());
    assertType('Pest\Expectation<string>', expect($string)->toBeFalsy());
    assertType('Pest\Expectation<string>', expect($string)->toContain('h'));
    assertType('Pest\Expectation<string>', expect($string)->toStartWith('h'));
    assertType('Pest\Expectation<string>', expect($string)->toEndWith('o'));
    assertType('Pest\Expectation<string>', expect($string)->toMatch('/h/'));
    assertType('Pest\Expectation<string>', expect($string)->toHaveLength(5));
    assertType('Pest\Expectation<string>', expect($string)->toBeUppercase());
    assertType('Pest\Expectation<string>', expect($string)->toBeLowercase());
    assertType('Pest\Expectation<string>', expect($string)->dump());
}

function testComparisonMatchersPreserveType(): void
{
    /** @var int $int */
    $int = 5;
    assertType('Pest\Expectation<int>', expect($int)->toBeGreaterThan(1));
    assertType('Pest\Expectation<int>', expect($int)->toBeGreaterThanOrEqual(5));
    assertType('Pest\Expectation<int>', expect($int)->toBeLessThan(10));
    assertType('Pest\Expectation<int>', expect($int)->toBeLessThanOrEqual(5));
    assertType('Pest\Expectation<int>', expect($int)->toBeBetween(1, 10));
    assertType('Pest\Expectation<int>', expect($int)->toEqualCanonicalizing(5));
}

function testCollectionMatchersPreserveType(): void
{
    /** @var array<string, int> $data */
    $data = ['a' => 1];
    assertType('Pest\Expectation<array<string, int>>', expect($data)->toHaveKey('a'));
    assertType('Pest\Expectation<array<string, int>>', expect($data)->toHaveKeys(['a']));
    assertType('Pest\Expectation<array<string, int>>', expect($data)->toHaveCount(1));
    assertType('Pest\Expectation<array<string, int>>', expect($data)->toMatchArray(['a' => 1]));
    assertType('Pest\Expectation<array<string, int>>', expect($data)->toContain(1));
}

function testOppositeExpectation(): void
{
    assertType('Pest\Expectations\OppositeExpectation<1>', expect(1)->not);
    assertType('Pest\Expectations\OppositeExpectation<1>', expect(1)->not());
}

function testEachExpectation(): void
{
    /** @var array{int, int} $arr */
    $arr = [1, 2];
    assertType('Pest\Expectations\EachExpectation<array{int, int}>', expect($arr)->each());
}

function testJsonExpectation(): void
{
    assertType('Pest\Expectation<array<int|string, mixed>|bool>', expect('{"a":1}')->json());
}

function testTerminalMatchers(): void
{
    assertType('never', expect('x')->dd());
    assertType('never', expect('x')->ddWhen(true));
}

function testDeepChains(): void
{
    /** @var int|string $value */
    $value = 1;
    assertType('Pest\Expectation<int>', expect($value)->toBeInt()->toBeGreaterThan(0)->toBe(1));

    assertType('Pest\Expectation<string>', expect($value)->toBeString()->toStartWith('a')->toEndWith('b'));
}

function testChainWithAnd(): void
{
    /** @var string $string */
    $string = 'a';
    assertType('Pest\Expectation<int>', expect($string)->toBeString()->and(42));
    assertType('Pest\Expectation<int>', expect($string)->toBeString()->and(42)->toBeInt());
    assertType('Pest\Expectation<int>', expect($string)->and(1)->and(2));
}

function testChainWithNot(): void
{
    assertType('Pest\Expectation<1>', expect(1)->not->toBe(2));
    assertType('Pest\Expectation<1>', expect(1)->not->toBe(2)->not->toBe(3));
    assertType('Pest\Expectation<1>', expect(1)->not()->toBe(2)->not()->toBe(3));
}

function testNotNarrowsThenChains(): void
{
    /** @var int|string $value */
    $value = 1;
    assertType('Pest\Expectation<int>', expect($value)->toBeInt()->not->toBe(2));
}

function testWhenAndUnless(): void
{
    /** @var string $string */
    $string = 'a';
    assertType('Pest\Expectation<string>', expect($string)->when(true, fn ($e) => $e));
    assertType('Pest\Expectation<string>', expect($string)->unless(false, fn ($e) => $e));
}

function testCountableValues(): void
{
    /** @var Countable&Traversable<int, int> $countable */
    $countable = new ArrayIterator([1]);
    assertType('Pest\Expectation<Countable&Traversable<int, int>>', expect($countable)->toHaveCount(1));
}

function testStringableChains(): void
{
    /** @var Stringable $stringable */
    $stringable = new class implements Stringable
    {
        public function __toString(): string
        {
            return 'x';
        }
    };
    assertType('Pest\Expectation<Stringable>', expect($stringable)->toBeInstanceOf(Stringable::class));
}
