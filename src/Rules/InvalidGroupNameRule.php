<?php

declare(strict_types=1);

namespace Pest\PHPStan\Rules;

use Pest\Configuration;
use Pest\PendingCalls\TestCall;
use Pest\PendingCalls\UsesCall;
use Pest\PHPStan\Diagnostics\PestDiagnosticIdentifiers;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

/**
 * @implements Rule<MethodCall>
 */
final class InvalidGroupNameRule implements Rule
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier || $node->name->name !== 'group') {
            return [];
        }

        $callerType = $scope->getType($node->var);
        if (! $this->isSupportedGroupCaller($callerType)) {
            return [];
        }

        $args = $node->getArgs();
        if ($args === []) {
            return [
                RuleErrorBuilder::message('group() requires at least one non-empty string argument.')
                    ->identifier(PestDiagnosticIdentifiers::GROUP_INVALID_NAME)
                    ->build(),
            ];
        }

        foreach ($args as $arg) {
            if ($arg->value instanceof String_ && mb_trim($arg->value->value) === '') {
                return [
                    RuleErrorBuilder::message('group() requires a non-empty string argument.')
                        ->identifier(PestDiagnosticIdentifiers::GROUP_INVALID_NAME)
                        ->build(),
                ];
            }
        }

        return [];
    }

    private function isSupportedGroupCaller(Type $callerType): bool
    {
        return array_any([TestCall::class, UsesCall::class, Configuration::class], fn (string $supportedCaller): bool => new ObjectType($supportedCaller)->isSuperTypeOf($callerType)->yes());
    }
}
