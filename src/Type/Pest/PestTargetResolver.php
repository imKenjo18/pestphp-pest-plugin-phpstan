<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\String_;

final class PestTargetResolver
{
    public function __construct(
        private readonly PestFileDiscoverer $fileDiscoverer,
    ) {}

    /**
     * @return list<string>
     */
    public function resolveInTargets(MethodCall $inMethodCall, string $pestFileDir): array
    {
        $keys = [];

        foreach ($inMethodCall->getArgs() as $arg) {
            $target = $this->resolveTargetArg($arg->value);
            if ($target === null) {
                continue;
            }

            $path = $this->isAbsolutePath($target) ? $target : $pestFileDir.'/'.$target;

            array_push($keys, ...$this->expandTarget($path));
        }

        return array_values(array_unique($keys));
    }

    public function matches(string $bindingKey, string $normalizedFilePath): bool
    {
        if (str_ends_with($bindingKey, '/')) {
            return str_starts_with($normalizedFilePath, $bindingKey);
        }

        return $normalizedFilePath === $bindingKey;
    }

    private function resolveTargetArg(Expr $value): ?string
    {
        if ($value instanceof String_) {
            return $value->value;
        }

        if ($value instanceof Dir) {
            return '.';
        }

        if ($value instanceof Concat) {
            $left = $this->resolveTargetArg($value->left);
            $right = $this->resolveTargetArg($value->right);

            if ($left === null || $right === null) {
                return null;
            }

            return $left.$right;
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function expandTarget(string $path): array
    {
        $matches = glob($path);

        if ($matches === false || $matches === []) {
            return [$this->fileDiscoverer->normalizePath($path).'/'];
        }

        $keys = [];
        foreach ($matches as $match) {
            $normalized = $this->fileDiscoverer->normalizePath($match);
            $keys[] = is_dir($match) ? $normalized.'/' : $normalized;
        }

        return $keys;
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, '/')
            || str_starts_with($path, '\\')
            || preg_match('/^[A-Za-z]:[\/\\\\]/', $path) === 1;
    }
}
