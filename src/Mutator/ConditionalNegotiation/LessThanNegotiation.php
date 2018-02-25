<?php
/**
 * Copyright © 2017-2018 Maks Rafalko
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

declare(strict_types=1);

namespace Infection\Mutator\ConditionalNegotiation;

use Infection\Mutator\Mutator;
use PhpParser\Node;

class LessThanNegotiation extends Mutator
{
    /**
     * Replaces "<" with ">="
     *
     * @param Node $node
     *
     * @return Node\Expr\BinaryOp\GreaterOrEqual
     */
    public function mutate(Node $node)
    {
        return new Node\Expr\BinaryOp\GreaterOrEqual($node->left, $node->right, $node->getAttributes());
    }

    public function shouldMutate(Node $node): bool
    {
        return $node instanceof Node\Expr\BinaryOp\Smaller;
    }
}
