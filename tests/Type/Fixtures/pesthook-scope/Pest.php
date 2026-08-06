<?php

declare(strict_types=1);

use Tests\Type\Fixtures\CustomTestCase;

pest()->extend(CustomTestCase::class)->beforeEach(function (): void {
    $this->scopedProperty = 'scoped';
})->in('Scoped');

pest()->extend(CustomTestCase::class)->beforeEach(fn (): string => $this->arrowScopedProperty = 'arrow-scoped')->in('ArrowScoped');

pest()->extend(CustomTestCase::class)->beforeEach(function (): void {
    $this->untargetedProperty = 'untargeted';
});
