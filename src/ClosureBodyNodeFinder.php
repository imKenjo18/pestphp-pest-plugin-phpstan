<?php

declare(strict_types=1);

namespace Pest\PHPStan;

use Closure;
use PhpParser\Node;
use PhpParser\Node\Expr\Closure as ClosureExpr;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;

final class ClosureBodyNodeFinder
{
    /**
     * @param  Closure(Node): bool  $predicate
     * @return list<Node>
     */
    public static function find(ClosureExpr $closure, Closure $predicate): array
    {
        $visitor = new class($predicate) extends NodeVisitorAbstract
        {
            /** @var list<Node> */
            public array $found = [];

            /**
             * @param  Closure(Node): bool  $predicate
             */
            public function __construct(
                private readonly Closure $predicate,
            ) {}

            public function enterNode(Node $node): ?int
            {
                if ($node instanceof ClosureExpr) {
                    return NodeVisitor::DONT_TRAVERSE_CHILDREN;
                }

                if (($this->predicate)($node)) {
                    $this->found[] = $node;
                }

                return null;
            }
        };

        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);
        $traverser->traverse($closure->stmts);

        return $visitor->found;
    }
}
