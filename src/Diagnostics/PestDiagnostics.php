<?php

declare(strict_types=1);

namespace Pest\PHPStan\Diagnostics;

use Pest\PHPStan\Analysis\Expectation\MatcherRequirementRegistry;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;

final class PestDiagnostics
{
    public static function invalidMatcherType(string $matcher, string $valueType, string $requirement): PestDiagnostic
    {
        return new PestDiagnostic(
            identifier: self::identifierForRequirement($requirement),
            message: sprintf(
                'Calling %s() on Expectation<%s>; matcher requires %s.',
                $matcher,
                $valueType,
                self::requirementLabel($requirement),
            ),
            tip: sprintf(
                'Pass %s value to expect() before calling %s().',
                self::requirementValuePhrase($requirement),
                $matcher,
            ),
        );
    }

    public static function impossibleExpectation(string $matcher, string $valueType): PestDiagnostic
    {
        return new PestDiagnostic(
            identifier: PestDiagnosticIdentifiers::EXPECTATION_IMPOSSIBLE,
            message: sprintf('Calling %s() on Expectation<%s>; assertion is impossible.', $matcher, $valueType),
            tip: sprintf('The expectation value is %s, which can never satisfy %s().', $valueType, $matcher),
        );
    }

    public static function redundantExpectation(string $matcher, string $valueType): PestDiagnostic
    {
        return new PestDiagnostic(
            identifier: PestDiagnosticIdentifiers::EXPECTATION_REDUNDANT,
            message: sprintf('Calling %s() on Expectation<%s>; assertion is redundant.', $matcher, $valueType),
            tip: sprintf('The expectation value is already guaranteed to satisfy %s().', $matcher),
        );
    }

    public static function invalidLifecycleThisUsage(
        string $hook,
        string $replacementHook,
        string $identifier,
        int $line,
    ): PestDiagnostic {
        return new PestDiagnostic(
            identifier: $identifier,
            message: sprintf('%s() runs in static context — $this is not available. Use %s() instead.', $hook, $replacementHook),
            line: $line,
        );
    }

    public static function toRuleError(PestDiagnostic $diagnostic): IdentifierRuleError
    {
        $builder = RuleErrorBuilder::message($diagnostic->message)
            ->identifier($diagnostic->identifier);

        if ($diagnostic->tip !== null) {
            $builder->tip($diagnostic->tip);
        }

        if ($diagnostic->line !== null) {
            $builder->line($diagnostic->line);
        }

        return $builder->build();
    }

    private static function identifierForRequirement(string $requirement): string
    {
        return match ($requirement) {
            MatcherRequirementRegistry::ITERABLE => PestDiagnosticIdentifiers::EXPECTATION_REQUIRES_ITERABLE,
            MatcherRequirementRegistry::COUNTABLE_OR_ITERABLE => PestDiagnosticIdentifiers::EXPECTATION_REQUIRES_COUNTABLE_OR_ITERABLE,
            default => PestDiagnosticIdentifiers::EXPECTATION_REQUIRES_STRING,
        };
    }

    private static function requirementLabel(string $requirement): string
    {
        return match ($requirement) {
            MatcherRequirementRegistry::COUNTABLE_OR_ITERABLE => 'countable or iterable',
            default => $requirement,
        };
    }

    private static function requirementValuePhrase(string $requirement): string
    {
        return match ($requirement) {
            MatcherRequirementRegistry::ITERABLE => 'an iterable',
            MatcherRequirementRegistry::COUNTABLE_OR_ITERABLE => 'a countable or iterable',
            default => 'a string',
        };
    }
}
