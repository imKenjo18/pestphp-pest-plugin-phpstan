<?php

declare(strict_types=1);

use Tests\CustomTestCaseTestCase;

test('custom testcase closure types', function (string $assertType, string $file, mixed ...$args): void {
    $this->assertFileAsserts($assertType, $file, ...$args);
})->with(function (): Iterator {
    yield from CustomTestCaseTestCase::gatherAssertTypes(__DIR__.'/../Fixtures/CustomTestCaseInference/Feature/test-closures-custom-testcase.php');
});
