<?php

declare(strict_types=1);

use Tests\UsesHookClosureThisTestCase;

test('hook closures in a uses()/pest() chain bind $this to the extended test case', function (string $assertType, string $file, mixed ...$args): void {
    $this->assertFileAsserts($assertType, $file, ...$args);
})->with(function (): Iterator {
    yield from UsesHookClosureThisTestCase::gatherAssertTypes(__DIR__.'/../Fixtures/UsesHookClosureThis/Pest.php');
});
