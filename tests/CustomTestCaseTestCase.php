<?php

declare(strict_types=1);

namespace Tests;

use Override;
use PHPStan\Testing\TypeInferenceTestCase;

abstract class CustomTestCaseTestCase extends TypeInferenceTestCase
{
    /**
     * @return string[]
     */
    #[Override]
    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__ . '/Type/custom-testcase-extension.neon',
        ];
    }
}
