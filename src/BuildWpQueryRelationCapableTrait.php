<?php

namespace RebelCode\WordPress\Query\Builder;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Expression\TermInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use OutOfRangeException;

/**
 * Common functionality for objects that can build the relation portions of `WP_Query` array args.
 *
 * The relational portions can be found in meta queries and tax queries, and usually take the form of:
 *
 * ```php
 * <?php
 * [
 *     'relation' => 'AND/OR',
 *     [
 *         // ...
 *     ]
 *     // ...
 * ]
 * ```
 *
 * @since [*next-version*]
 */
trait BuildWpQueryRelationCapableTrait
{
    /**
     * Builds a given logical expression into a WP_Query relation array args portion.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression to build.
     * @param string|Stringable|null     $mode       Optional relation mode to distinguish between meta and tax mode.
     *
     * @return array The built expression, as the sub-array portion that represents it in WP_Query args.
     *
     * @throws OutOfRangeException If the given expression could not be built as a `WP_Query` relation.
     */
    protected function _buildWpQueryRelation(LogicalExpressionInterface $expression, $mode = null)
    {
        try {
            $relation = $this->_getWpQueryRelationOperator($expression);
            $result = [
                'relation' => $relation,
            ];

            foreach ($expression->getTerms() as $_term) {
                $result[] = $this->_buildWpQueryRelationTerm($_term, $expression, $mode);
            }

            return $result;
        } catch (RootException $exception) {
            throw $this->_createOutOfRangeException(
                $this->__('Expression is not a valid WP_Query relation'),
                null,
                $exception,
                $expression
            );
        }
    }

    /**
     * Retrieves the WP_Query relation operator from an expression.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to extract from.
     *
     * @throws OutOfRangeException If no relation operator could be determined for the given expression.
     *
     * @return string The relation operator string.
     */
    abstract protected function _getWpQueryRelationOperator(LogicalExpressionInterface $expression);

    /**
     * Builds a term into a WP_Query array args portion.
     *
     * @since [*next-version*]
     *
     * @param TermInterface              $term   The term to build.
     * @param LogicalExpressionInterface $parent The parent expression of the term.
     * @param string|Stringable|null     $mode   Optional relation mode to distinguish between meta and tax mode.
     *
     * @throws OutOfRangeException If the term could not be built.
     *
     * @return array The built term as the sub-array portion that represents it in WP_Query args.
     */
    abstract protected function _buildWpQueryRelationTerm(
        TermInterface $term,
        LogicalExpressionInterface $parent,
        $mode = null
    );

    /**
     * Creates a new Out Of Range exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return OutOfRangeException The new exception.
     */
    abstract protected function _createOutOfRangeException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $argument = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
