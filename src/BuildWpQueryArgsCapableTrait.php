<?php

namespace RebelCode\WordPress\Query\Builder;

use Dhii\Expression\LogicalExpressionInterface;
use Dhii\Expression\Type\BooleanTypeInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;
use OutOfRangeException;

/**
 * Common functionality for objects that can build expressions into
 * {@link https://codex.wordpress.org/Class_Reference/WP_Query `WP_Query`} array arguments.
 *
 * @since [*next-version*]
 */
trait BuildWpQueryArgsCapableTrait
{
    /**
     * Builds a given expression into array arguments that can be passed to `WP_Query`.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression to build into the `WP_Query` array argument.
     *
     * @throws OutOfRangeException If the given expression or any of its terms are not supported.
     *
     * @return array The resulting `WP_Query` array argument.
     */
    protected function _buildWpQueryArgs(LogicalExpressionInterface $expression)
    {
        if (!$this->_isWpQueryExpressionSupported($expression)) {
            throw $this->_createOutOfRangeException(
                $this->__('Expression is not supported'),
                null,
                null,
                $expression
            );
        }

        $query = [];

        foreach ($expression->getTerms() as $_term) {
            if (!($_term instanceof LogicalExpressionInterface)) {
                throw $this->_createOutOfRangeException(
                    $this->__('Expression term is not a logical expression'),
                    null,
                    null,
                    $_term
                );
            }

            $query = array_merge($query, $this->_attemptBuildWpQueryArgsTerm($_term));
        }

        return $query;
    }

    /**
     * Attempts to build an expression as top-level `WP_Query` args term.
     *
     * This algorithm exploits the fact that the delegate build methods are meant to throw exceptions on failure.
     * Catching an exception therefore means that the expression cannot be built using that method, and so the
     * algorithm attempts to use another.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression to attempt to build.
     *
     * @throws OutOfRangeException If no building mechanism could be determined for the given expression.
     *
     * @return array The resulting array, as a portion of the final result.
     */
    protected function _attemptBuildWpQueryArgsTerm(LogicalExpressionInterface $expression)
    {
        // Attempt to build as a meta query
        try {
            return ['meta_query' => $this->_buildWpQueryMetaRelation($expression)];
        } catch (OutOfRangeException $exception) {
        }

        // Attempt to build as a taxonomy query
        try {
            return ['tax_query' => $this->_buildWpQueryTaxRelation($expression)];
        } catch (OutOfRangeException $exception) {
        }

        // Attempt to build as a top-level compare key-value entry
        try {
            return $this->_buildWpQueryCompare($expression);
        } catch (OutOfRangeException $exception) {
        }

        throw $this->_createOutOfRangeException(
            $this->__('Expression could not be built - no supported build method found'),
            null,
            null,
            $expression
        );
    }

    /**
     * Checks whether a given expression is supported for building WP_Query array arguments.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression instance to check.
     *
     * @return bool True if the expression is supported, false if not.
     */
    protected function _isWpQueryExpressionSupported(LogicalExpressionInterface $expression)
    {
        return $this->_normalizeString($expression->getType()) === BooleanTypeInterface::T_AND;
    }

    /**
     * Builds a given logical expression into a WP_Query compare array args portion.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression to build.
     *
     * @throws OutOfRangeException If the given expression could not be built into a WP_Query compare.
     *
     * @return array The built expression, as the sub-array portion that represents it in WP_Query args.
     */
    abstract protected function _buildWpQueryCompare(LogicalExpressionInterface $expression);

    /**
     * Builds a given logical expression into a WP_Query meta relation array args portion.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression to build.
     *
     * @throws OutOfRangeException If the given expression could not be built into a WP_Query meta relation.
     *
     * @return array The built meta relation sub-array portion that represents it in WP_Query args.
     */
    abstract protected function _buildWpQueryMetaRelation(LogicalExpressionInterface $expression);

    /**
     * Builds a given logical expression into a WP_Query taxonomy relation array args portion.
     *
     * @since [*next-version*]
     *
     * @param LogicalExpressionInterface $expression The expression to build.
     *
     * @throws OutOfRangeException If the given expression could not be built into a WP_Query taxonomy relation.
     *
     * @return array The built taxonomy relation sub-array portion that represents it in WP_Query args.
     */
    abstract protected function _buildWpQueryTaxRelation(LogicalExpressionInterface $expression);

    /**
     * Normalizes a value to its string representation.
     *
     * The values that can be normalized are any scalar values, as well as
     * {@see StringableInterface).
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $subject The value to normalize to string.
     *
     * @throws InvalidArgumentException If the value cannot be normalized.
     *
     * @return string The string that resulted from normalization.
     */
    abstract protected function _normalizeString($subject);

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
