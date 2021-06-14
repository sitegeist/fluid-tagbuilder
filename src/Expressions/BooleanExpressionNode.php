<?php

declare(strict_types=1);

namespace Sitegeist\FluidTagbuilder\Expressions;

use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\Expression\AbstractExpressionNode;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\Expression\ExpressionException;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\Parser\BooleanParser;

class BooleanExpressionNode extends AbstractExpressionNode
{
    /**
     * Pattern which detects ternary conditions written in shorthand
     * syntax, e.g. {some.variable as integer}. The right-hand side
     * of the expression can also be a variable containing the type
     * of the variable.
     */
    public static $detectionExpression = '/
        {                                # Start of shorthand syntax
            (
                \(*!*
                [_a-zA-Z0-9.]+
                [\s]+[=|&!><%]
                .*?
            )
        }                                # End of shorthand syntax
        /x';

    /**
     * @param RenderingContextInterface $renderingContext
     * @param string $expression
     * @param array $matches
     * @return integer|float
     */
    public static function evaluateExpression(RenderingContextInterface $renderingContext, $expression, array $matches)
    {
        $parser = new BooleanParser();
        return $parser->evaluate($matches[1], $renderingContext->getVariableProvider()->getAll());
    }
}
