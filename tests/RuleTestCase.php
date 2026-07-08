<?php

declare(strict_types=1);

namespace Tests;

use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase as RuleTestCaseCore;

abstract class RuleTestCase extends RuleTestCaseCore
{
    public static Rule $rule;

    /**
     * @var string[]
     */
    public static array $additionalConfigFiles = [];

    /**
     * @return string[]
     */
    #[Override]
    final public static function getAdditionalConfigFiles(): array
    {
        return self::$additionalConfigFiles;
    }

    /**
     * @param  class-string<Rule>  $ruleClass
     */
    final public static function resolveRule(string $ruleClass): Rule
    {
        return self::getContainer()->getByType($ruleClass);
    }

    protected function getRule(): Rule
    {
        return self::$rule;
    }
}
