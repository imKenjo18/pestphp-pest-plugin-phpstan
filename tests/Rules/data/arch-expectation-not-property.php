<?php

declare(strict_types=1);

test('not property on arch expectation is allowed', function (): void {
    expect('App')->toUseStrictTypes()->not->toUse(['dd', 'dump']);
    expect('App')->toUseStrictTypes()->not->toBeFinal();
    expect(['App\Models', 'App\Services'])->toUseStrictTypes()->not->toUse('Illuminate\Support\Facades\DB');
});
