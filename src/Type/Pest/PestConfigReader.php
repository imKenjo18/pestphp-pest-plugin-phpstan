<?php

declare(strict_types=1);

namespace Pest\PHPStan\Type\Pest;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeFinder;

final class PestConfigReader
{
    /** @var array<string, list<string>> Maps normalized directory paths to fully-qualified class/trait names */
    private array $directoryMap = [];

    /** @var array<string, list<array{class: string, config: string}>> */
    private array $globalUseDirectoryMap = [];

    /** @var array<string, list<string>> Caches bindings declared directly inside a test file */
    private array $fileBindingsCache = [];

    private bool $parsed = false;

    public function __construct(
        private readonly PestFileDiscoverer $fileDiscoverer,
    ) {}

    /**
     * @return list<string>
     */
    public function resolveBindings(string $filePath): array
    {
        $this->ensureParsed();

        $normalizedFile = $this->fileDiscoverer->normalizePath($filePath);
        $bindings = [];

        foreach ($this->directoryMap as $directory => $classNames) {
            if (! str_starts_with($normalizedFile, $directory)) {
                continue;
            }

            array_push($bindings, ...$classNames);
        }

        return array_values(array_unique($bindings));
    }

    /**
     * @return list<string>
     */
    public function resolveFileBindings(string $filePath): array
    {
        $normalizedFile = $this->fileDiscoverer->normalizePath($filePath);

        if (isset($this->fileBindingsCache[$normalizedFile])) {
            return $this->fileBindingsCache[$normalizedFile];
        }

        $parsed = $this->fileDiscoverer->parseFile($filePath);
        if ($parsed === null) {
            return $this->fileBindingsCache[$normalizedFile] = [];
        }

        [$stmts] = $parsed;

        $bindings = [];
        foreach ($this->topLevelExpressions($stmts) as $expr) {
            $usesArgs = $this->extractFileUsesArgs($expr);
            if ($usesArgs !== []) {
                array_push($bindings, ...$usesArgs);

                continue;
            }

            $pestArgs = $this->extractFilePestArgs($expr);
            if ($pestArgs !== []) {
                array_push($bindings, ...$pestArgs);
            }
        }

        return $this->fileBindingsCache[$normalizedFile] = array_values(array_unique($bindings));
    }

    /**
     * @return list<array{class: string, config: string}>
     */
    public function resolveGlobalUses(string $filePath): array
    {
        $this->ensureParsed();

        $normalizedFile = $this->fileDiscoverer->normalizePath($filePath);
        $bindings = [];

        foreach ($this->globalUseDirectoryMap as $directory => $directoryBindings) {
            if (str_starts_with($normalizedFile, $directory)) {
                array_push($bindings, ...$directoryBindings);
            }
        }

        return $bindings;
    }

    /**
     * @return list<string>
     */
    public function allBoundClasses(): array
    {
        $this->ensureParsed();

        $bindings = [];

        foreach ($this->directoryMap as $classNames) {
            array_push($bindings, ...$classNames);
        }

        return array_values(array_unique($bindings));
    }

    private function ensureParsed(): void
    {
        if ($this->parsed) {
            return;
        }

        $this->parsed = true;

        $pestFiles = $this->fileDiscoverer->discoverPestFiles();

        foreach ($pestFiles as $pestFile) {
            $this->parsePestFile($pestFile);
        }
    }

    private function parsePestFile(string $filePath): void
    {
        $parsed = $this->fileDiscoverer->parseFile($filePath);
        if ($parsed === null) {
            return;
        }

        [$stmts] = $parsed;

        $nodeFinder = new NodeFinder;
        $pestFileDir = dirname($filePath);

        $this->extractUsesBindings($nodeFinder, $stmts, $pestFileDir, $filePath);
        $this->extractPestBindings($nodeFinder, $stmts, $pestFileDir, $filePath);
    }

    /**
     * @param  Node[]  $stmts
     */
    private function extractUsesBindings(NodeFinder $nodeFinder, array $stmts, string $pestFileDir, string $pestFile): void
    {
        $methodCalls = $nodeFinder->findInstanceOf($stmts, MethodCall::class);

        foreach ($methodCalls as $methodCall) {
            if (! $this->fileDiscoverer->isMethodNamed($methodCall, 'in')) {
                continue;
            }

            $classNames = $this->resolveUsesClassNames($methodCall->var);
            if ($classNames === []) {
                continue;
            }

            $this->registerDirectoryBindings($classNames, $methodCall, $pestFileDir);
            $this->registerGlobalUseBindings($classNames, $methodCall, $pestFileDir, $pestFile);
        }

        $funcCalls = $nodeFinder->findInstanceOf($stmts, FuncCall::class);
        foreach ($funcCalls as $funcCall) {
            if (! $this->isFuncNamed($funcCall, 'uses')) {
                continue;
            }

            if ($this->hasInChain($funcCall, $stmts, $nodeFinder)) {
                continue;
            }

            $classNames = $this->extractAllClassArgs($funcCall);
            if ($classNames !== []) {
                $this->appendBindings($this->fileDiscoverer->normalizePath($pestFileDir).'/', $classNames);
            }
        }
    }

    /**
     * @param  Node[]  $stmts
     */
    private function extractPestBindings(NodeFinder $nodeFinder, array $stmts, string $pestFileDir, string $pestFile): void
    {
        $methodCalls = $nodeFinder->findInstanceOf($stmts, MethodCall::class);

        foreach ($methodCalls as $methodCall) {
            if (! $this->fileDiscoverer->isMethodNamed($methodCall, 'in')) {
                continue;
            }

            $pestBindings = $this->resolvePestClassNames($methodCall->var);
            if ($pestBindings === null) {
                continue;
            }

            $classNames = [...$pestBindings['extend'], ...$pestBindings['use']];
            if ($classNames !== []) {
                $this->registerDirectoryBindings($classNames, $methodCall, $pestFileDir);
            }

            if ($pestBindings['use'] !== []) {
                $this->registerGlobalUseBindings($pestBindings['use'], $methodCall, $pestFileDir, $pestFile);
            }
        }

        foreach ($methodCalls as $methodCall) {
            if (! $this->isPestBindingMethod($methodCall)) {
                continue;
            }

            if (! $this->isPestFuncCall($methodCall->var)) {
                continue;
            }

            if ($this->isPartOfInChain($methodCall, $stmts, $nodeFinder)) {
                continue;
            }

            $classNames = $this->extractAllClassArgs($methodCall);
            if ($classNames !== []) {
                $this->appendBindings($this->fileDiscoverer->normalizePath($pestFileDir).'/', $classNames);
            }
        }
    }

    /**
     * @return list<string>
     */
    private function resolveUsesClassNames(Expr $expr): array
    {
        if ($expr instanceof FuncCall && $this->isFuncNamed($expr, 'uses')) {
            return $this->extractAllClassArgs($expr);
        }

        if ($expr instanceof MethodCall) {
            return $this->resolveUsesClassNames($expr->var);
        }

        return [];
    }

    /**
     * @return array{extend: list<string>, use: list<string>}|null
     */
    private function resolvePestClassNames(Expr $expr): ?array
    {
        if ($this->isPestFuncCall($expr)) {
            return ['extend' => [], 'use' => []];
        }

        if (! $expr instanceof MethodCall) {
            return null;
        }

        $bindings = $this->resolvePestClassNames($expr->var);
        if ($bindings === null) {
            return null;
        }

        if ($this->isPestExtendMethod($expr)) {
            array_push($bindings['extend'], ...$this->extractAllClassArgs($expr));
        } elseif ($this->isPestUseMethod($expr)) {
            array_push($bindings['use'], ...$this->extractAllClassArgs($expr));
        }

        return $bindings;
    }

    private function isPestBindingMethod(MethodCall $methodCall): bool
    {
        if ($this->isPestExtendMethod($methodCall)) {
            return true;
        }

        return $this->isPestUseMethod($methodCall);
    }

    private function isPestExtendMethod(MethodCall $methodCall): bool
    {
        if ($this->fileDiscoverer->isMethodNamed($methodCall, 'extend')) {
            return true;
        }

        return $this->fileDiscoverer->isMethodNamed($methodCall, 'extends');
    }

    private function isPestUseMethod(MethodCall $methodCall): bool
    {
        if ($this->fileDiscoverer->isMethodNamed($methodCall, 'use')) {
            return true;
        }

        return $this->fileDiscoverer->isMethodNamed($methodCall, 'uses');
    }

    private function isPestFuncCall(Expr $expr): bool
    {
        return $expr instanceof FuncCall && $this->isFuncNamed($expr, 'pest');
    }

    /**
     * @return list<string>|null
     */
    private function extractInDirectories(MethodCall $methodCall): ?array
    {
        $directories = [];

        foreach ($methodCall->getArgs() as $arg) {
            $value = $arg->value;

            if ($value instanceof String_) {
                $directories[] = $value->value;

                continue;
            }

            return null;
        }

        return $directories;
    }

    /**
     * @param  list<string>  $classNames
     */
    private function registerDirectoryBindings(array $classNames, MethodCall $inMethodCall, string $pestFileDir): void
    {
        $directories = $this->extractInDirectories($inMethodCall);

        if ($directories === null || $directories === []) {
            return;
        }

        foreach ($directories as $dir) {
            $fullPath = $this->fileDiscoverer->normalizePath($pestFileDir.'/'.$dir).'/';
            $this->appendBindings($fullPath, $classNames);
        }
    }

    /**
     * @param  list<string>  $classNames
     */
    private function registerGlobalUseBindings(
        array $classNames,
        MethodCall $inMethodCall,
        string $pestFileDir,
        string $pestFile,
    ): void {
        $directories = $this->extractInDirectories($inMethodCall);
        if ($directories === null || $directories === []) {
            return;
        }

        foreach ($directories as $dir) {
            $directory = $this->fileDiscoverer->normalizePath($pestFileDir.'/'.$dir).'/';
            $this->globalUseDirectoryMap[$directory] ??= [];

            foreach ($classNames as $className) {
                $this->globalUseDirectoryMap[$directory][] = [
                    'class' => $className,
                    'config' => $this->fileDiscoverer->normalizePath($pestFile),
                ];
            }
        }
    }

    /**
     * @param  list<string>  $classNames
     */
    private function appendBindings(string $directory, array $classNames): void
    {
        if (! isset($this->directoryMap[$directory])) {
            $this->directoryMap[$directory] = [];
        }

        array_push($this->directoryMap[$directory], ...$classNames);
    }

    /**
     * @return list<string>
     */
    private function extractAllClassArgs(FuncCall|MethodCall $call): array
    {
        $classNames = [];

        foreach ($call->getArgs() as $arg) {
            $value = $arg->value;

            if ($value instanceof ClassConstFetch && $value->name instanceof Identifier && $value->name->toString() === 'class' && $value->class instanceof Name) {
                $classNames[] = $value->class->toString();

                continue;
            }

            if ($value instanceof String_) {
                $classNames[] = $value->value;
            }
        }

        return $classNames;
    }

    private function isFuncNamed(FuncCall $funcCall, string $name): bool
    {
        return $funcCall->name instanceof Name && $funcCall->name->toString() === $name;
    }

    /**
     * @param  Node[]  $stmts
     */
    private function hasInChain(FuncCall $funcCall, array $stmts, NodeFinder $nodeFinder): bool
    {
        $methodCalls = $nodeFinder->findInstanceOf($stmts, MethodCall::class);

        foreach ($methodCalls as $methodCall) {
            if (! $this->fileDiscoverer->isMethodNamed($methodCall, 'in')) {
                continue;
            }

            if ($this->resolveUsesClassNames($methodCall->var) !== [] && $this->chainContainsNode($methodCall->var, $funcCall)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  Node[]  $stmts
     */
    private function isPartOfInChain(MethodCall $bindingCall, array $stmts, NodeFinder $nodeFinder): bool
    {
        $methodCalls = $nodeFinder->findInstanceOf($stmts, MethodCall::class);

        foreach ($methodCalls as $methodCall) {
            if (! $this->fileDiscoverer->isMethodNamed($methodCall, 'in')) {
                continue;
            }

            if ($this->chainContainsNode($methodCall->var, $bindingCall)) {
                return true;
            }
        }

        return false;
    }

    private function chainContainsNode(Expr $expr, Expr $target): bool
    {
        if ($expr === $target) {
            return true;
        }

        if ($expr instanceof MethodCall) {
            return $this->chainContainsNode($expr->var, $target);
        }

        return false;
    }

    /**
     * @param  Node[]  $stmts
     * @return list<Expr>
     */
    private function topLevelExpressions(array $stmts): array
    {
        $expressions = [];

        foreach ($stmts as $stmt) {
            if ($stmt instanceof Namespace_) {
                foreach ($stmt->stmts as $namespacedStmt) {
                    if ($namespacedStmt instanceof Expression) {
                        $expressions[] = $namespacedStmt->expr;
                    }
                }

                continue;
            }

            if ($stmt instanceof Expression) {
                $expressions[] = $stmt->expr;
            }
        }

        return $expressions;
    }

    /**
     * @return list<string>
     */
    private function extractFileUsesArgs(Expr $expr): array
    {
        if ($this->chainHasIn($expr)) {
            return [];
        }

        $root = $this->rootFuncCall($expr);
        if (! $root instanceof FuncCall || ! $this->isFuncNamed($root, 'uses')) {
            return [];
        }

        return $this->extractAllClassArgs($root);
    }

    /**
     * @return list<string>
     */
    private function extractFilePestArgs(Expr $expr): array
    {
        if (! $expr instanceof MethodCall || $this->chainHasIn($expr)) {
            return [];
        }

        $pestBindings = $this->resolvePestClassNames($expr);
        if ($pestBindings === null) {
            return [];
        }

        return [...$pestBindings['extend'], ...$pestBindings['use']];
    }

    private function rootFuncCall(Expr $expr): ?FuncCall
    {
        while ($expr instanceof MethodCall) {
            $expr = $expr->var;
        }

        return $expr instanceof FuncCall ? $expr : null;
    }

    private function chainHasIn(Expr $expr): bool
    {
        while ($expr instanceof MethodCall) {
            if ($this->fileDiscoverer->isMethodNamed($expr, 'in')) {
                return true;
            }

            $expr = $expr->var;
        }

        return false;
    }
}
