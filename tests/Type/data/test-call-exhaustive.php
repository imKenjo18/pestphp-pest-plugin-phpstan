<?php

declare(strict_types=1);

namespace TestCallExhaustive;

use Pest\PendingCalls\AfterEachCall;
use Pest\PendingCalls\BeforeEachCall;
use Pest\PendingCalls\DescribeCall;
use Pest\PendingCalls\TestCall;
use Pest\PendingCalls\UsesCall;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function PHPStan\Testing\assertType;

function testItAndTestReturnTestCall(): void
{
    assertType(TestCall::class, it('works', function (): void {}));
    assertType(TestCall::class, test('works', function (): void {}));
    assertType(TestCall::class, todo('later'));
}

function testConfigurationCalls(): void
{
    assertType(BeforeEachCall::class, beforeEach(function (): void {}));
    assertType(AfterEachCall::class, afterEach(function (): void {}));
    assertType('null', beforeAll(function (): void {}));
    assertType('null', afterAll(function (): void {}));
    assertType(DescribeCall::class, describe('group', function (): void {}));
    assertType(UsesCall::class, uses(TestCase::class));
}

function testTestCallFluentMethods(): void
{
    assertType(TestCall::class, it('x', function (): void {})->with([1, 2]));
    assertType(TestCall::class, it('x', function (): void {})->group('unit'));
    assertType(TestCall::class, it('x', function (): void {})->skip());
    assertType(TestCall::class, it('x', function (): void {})->skip('reason'));
    assertType(TestCall::class, it('x', function (): void {})->only());
    assertType(TestCall::class, it('x', function (): void {})->todo());
    assertType(TestCall::class, it('x', function (): void {})->depends('other'));
    assertType(TestCall::class, it('x', function (): void {})->repeat(3));
    assertType(TestCall::class, it('x', function (): void {})->throws(RuntimeException::class));
    assertType(TestCall::class, it('x', function (): void {})->throwsNoExceptions());
    assertType(TestCall::class, it('x', function (): void {})->covers('App\Foo'));
}

function testTestCallPlatformMethods(): void
{
    assertType(TestCall::class, it('x', function (): void {})->skipOnWindows());
    assertType(TestCall::class, it('x', function (): void {})->skipOnMac());
    assertType(TestCall::class, it('x', function (): void {})->skipOnLinux());
    assertType(TestCall::class, it('x', function (): void {})->onlyOnWindows());
    assertType(TestCall::class, it('x', function (): void {})->onlyOnMac());
    assertType(TestCall::class, it('x', function (): void {})->onlyOnLinux());
}

function testTestCallLongChain(): void
{
    $result = it('x', function (): void {})
        ->with([1, 2])
        ->group('unit', 'feature')
        ->skip(false)
        ->repeat(2)
        ->depends('other')
        ->throws(RuntimeException::class);
    assertType(TestCall::class, $result);
}

function testUsesFluentMethods(): void
{
    assertType(UsesCall::class, uses(TestCase::class)->group('integration'));
    assertType(UsesCall::class, uses(TestCase::class)->in('Feature'));
}

function testPestExtendChain(): void
{
    assertType(UsesCall::class, pest()->extend(TestCase::class));
    assertType(UsesCall::class, pest()->extend(TestCase::class)->in('Feature'));
}

function testNestedDescribe(): void
{
    assertType(DescribeCall::class, describe('outer', function (): void {
        describe('inner', function (): void {
            it('works', function (): void {});
        });
    }));
}
