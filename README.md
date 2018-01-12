# RebelCode - Expression WP Query Builder - Abstract

[![Build Status](https://travis-ci.org/rebelcode/expression-wp-query-builder.svg?branch=develop)](https://travis-ci.org/rebelcode/expression-wp-query-builder)
[![Code Climate](https://codeclimate.com/github/rebelcode/expression-wp-query-builder/badges/gpa.svg)](https://codeclimate.com/github/rebelcode/expression-wp-query-builder)
[![Test Coverage](https://codeclimate.com/github/rebelcode/expression-wp-query-builder/badges/coverage.svg)](https://codeclimate.com/github/rebelcode/expression-wp-query-builder/coverage)
[![Latest Stable Version](https://poser.pugx.org/rebelcode/expression-wp-query-builder/version)](https://packagist.org/packages/rebelcode/expression-wp-query-builder)

Abstract functionality for building `WP_Query` args using expressions.

## Details

This package provides abstract functionality for the most implementation aspects of building [`WP_Query`] arguments from
expressions. The traits in this package are meant to compliment each other, while also remaining agnostic of the each
other's implementation details. Most, if not all, traits are designed to provide functionality that depends on
abstracted methods. Other traits in the package will offer implementations for those abstracted methods, while also
depending on their own abstracted methods.
 
## Traits

### [`BuildWpQueryArgsCapableTrait`]

Intended to provide the entry point functionality of building an expression into [`WP_Query`] args by attempting to
build each expression term as either a comparison, meta query relation entry or taxonomy query relation entry. 
 
- **Required implementations:**
  - `_buildWpQueryCompare()` - _fulfilled by [`BuildWpQueryCompareCapableTrait`](#buildwpquerycomparecapabletrait)_
  - `_buildWpQueryMetaRelation()` - _fulfilled indirectly by [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)_
  - `_buildWpQueryTaxTelation()` - _fulfilled indirectly by [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)_

----

### [`BuildWpQueryCompareCapableTrait`]

Provides functionality for building top-level comparison key-value pairs.
 
- **Required implementations:**
  - `_getWpQueryCompareKey()`
  - `_getWpQueryCompareValue()`
- **Compliments:**
  - [`BuildWpQueryArgsCapableTrait`](#buildwpqueryargscapabletrait)

----

### [`BuildWpQueryRelationCapableTrait`]

Provides functionality for building relation arrays.

- **Required implementations:**
  - `_getWpQueryRelationOperator()` - _fullfilled by [`GetWpQueryRelationOperatorCapableTrait`](#getwpqueryrelationoperatorcapabletrait)_
  - `_buildWpQueryRelationTerm()` - _fulfilled by [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)_
- **Fulfills:**
  - [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)

----

### [`BuildWpQueryRelationTermCapableTrait`]

Provides functionality for building the terms in a relation array, by delegating building mechanism used depending on the current relation context, i.e. `meta_query` relation or `tax_query` relation.

- **Required implementations:**
  - `_buildWpQueryMetaCompare()` - _fulfilled by [`BuildWpQueryMetaCompareCapableTrait`]_
  - `_buildWpQueryTaxCompare()` - _fulfilled by [`BuildWpQueryTaxCompareCapableTrait`]_
- **Compliments**
  - [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)

----

### [`BuildWpQueryMetaCompareCapableTrait`]

Provides functionality for building meta comparison arrays.

- **Required implementations:**
  - `_getWpQueryMetaCompareKey()`
  - `_getWpQueryMetaCompareValue()`
  - `_getWpQueryMetaCompareType()` - _fulfilled by [`GetWpQueryMetaCompareTypeCapableTrait`]_
  - `_getWpQueryMetaCompareOperator()` - _fulfilled by [`GetWpQueryMetaCompareOperatorCapableTrait`]_
- **Compliments:**
  - [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)

---

### [`BuildWpQueryTaxCompareCapableTrait`]

Provides functionality for building taxonomy comparison arrays.

- **Required implementations:**
  - `_getWpQueryTaxCompareTaxonomy()`
  - `_getWpQueryTaxCompareField()`
  - `_getWpQueryTaxCompareTerms()`
  - `_getWpQueryTaxCompareOperator()` - _fulfilled by [`GetWpQueryTaxCompareOperatorCapableTrait`](#getwpquerytaxcompareoperatorcapabletrait)_
- **Compliments:**
  - [`BuildWpQueryRelationTermCapableTrait`](#buildwpqueryrelationtermcapabletrait)

---

### [`GetWpQueryMetaCompareOperatorCapableTrait`]

Provides functionality for resolving the meta comparison compare type from an expression.

- **Compliments:**
  - [`BuildWpQueryMetaCompareCapableTrait`](#buildwpquerymetacomparecapabletrait)

---

### [`GetWpQueryMetaCompareTypeCapableTrait`]

Provides functionality for resolving the meta comparison value cast type from an expression.

- **Required implementations:**
  - `_getWpQueryMetaCompareValue()`
- **Compliments:**
  - [`BuildWpQueryMetaCompareCapableTrait`](#buildwpquerymetacomparecapabletrait)

---

### [`GetWpQueryTaxCompareOperatorCapableTrait`]

Provides functionality for resolving the taxonomy comparison operator from an expression.

- **Compliments:**
  - [`BuildWpQueryTaxCompareCapableTrait`](#buildwpquerytaxcomparecapabletrait)

---

### [`GetWpQueryRelationOperatorCapableTrait`]

Provides functionality for resolving the relation operator ("AND" or "OR") from an expression.

- **Compliments:**
  - [`BuildWpQueryRelationCapableTrait`](#buildwpqueryrelationcapabletrait)

---

[`WP_Query`]: https://codex.wordpress.org/Class_Reference/WP_Query

[`BuildWpQueryArgsCapableTrait`]: src/BuildWpQueryArgsCapableTrait.php
[`BuildWpQueryCompareCapableTrait`]: src/BuildWpQueryCompareCapableTrait.php
[`BuildWpQueryRelationCapableTrait`]: src/BuildWpQueryRelationCapableTrait.php
[`BuildWpQueryRelationTermCapableTrait`]: src/BuildWpQueryRelationTermCapableTrait.php
[`BuildWpQueryMetaCompareCapableTrait`]: src/BuildWpQueryMetaCompareCapableTrait.php
[`BuildWpQueryTaxCompareCapableTrait`]: src/BuildWpQueryTaxCompareCapableTrait.php
[`GetWpQueryRelationOperatorCapableTrait`]: src/GetWpQueryRelationOperatorCapableTrait.php
[`GetWpQueryMetaCompareTypeCapableTrait`]: src/GetWpQueryMetaCompareTypeCapableTrait.php
[`GetWpQueryMetaCompareOperatorCapableTrait`]: src/GetWpQueryMetaCompareOperatorCapableTrait.php
[`GetWpQueryTaxCompareOperatorCapableTrait`]: src/GetWpQueryTaxCompareOperatorCapableTrait.php
[`GetWpQueryRelationOperatorCapableTrait`]: src/GetWpQueryRelationOperatorCapableTrait.php
