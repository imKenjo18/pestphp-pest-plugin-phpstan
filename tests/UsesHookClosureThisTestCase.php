<?php

declare(strict_types=1);

namespace Tests;

use Override;
use PHPStan\Testing\TypeInferenceTestCase;

abstract class UsesHookClosureThisTestCase extends TypeInferenceTestCase
{
    /**
     * @return string[]
     */
    #[Override]
    final public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__.'/Type/uses-hook-closure-this-extension.neon',
        ];
    }
}
