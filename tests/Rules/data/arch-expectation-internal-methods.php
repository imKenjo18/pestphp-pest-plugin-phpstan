<?php

declare(strict_types=1);

test('arch expectation methods of an internal interface are allowed', function (): void {
    arch()->expect('App\Models')
        ->toBeClasses()
        ->toExtend('Illuminate\Database\Eloquent\Model')
        ->toOnlyBeUsedIn('App\Repositories')
        ->ignoring('App\Models\User');
});

test('arch expectation ignoring global functions is allowed', function (): void {
    arch()->expect('App\Services')
        ->toUseNothing()
        ->ignoringGlobalFunctions();
});
